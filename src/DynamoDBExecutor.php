<?php

namespace konnosena\DynamoDB;

use Aws\DynamoDb\Exception\DynamoDbException;
use Aws\Sqs\Exception\SqsException;
use Aws\Sqs\SqsClient;
use function GuzzleHttp\Promise\all;
use konnosena\DynamoDB\Command\DynamoDBDeleteItemBuilder;
use konnosena\DynamoDB\Command\DynamoDBPutItemBuilder;
use konnosena\DynamoDB\Command\DynamoDBUpdateItemBuilder;
use konnosena\DynamoDB\Common\DynamoDBFallbackableUpdateItemCommon;
use konnosena\DynamoDB\Exception\DynamoDB_Exception;
use konnosena\DynamoDB\Exception\DynamoDB_FallbackException;
use konnosena\DynamoDB\Exception\DynamoDB_RequestException;
use konnosena\DynamoDB\Master\DynamoDBErrorCode;
use Ramsey\Uuid\Uuid;


/**
 * Class DynamoDBQueryExecutor
 * 更新クエリを完遂保証する
 * @package konnosena\DynamoDB
 */
class DynamoDBExecutor
{
	const ID = "P001";
	
	/**
	 * @var SqsClient
	 */
	protected $sqs_client = null;
	protected $fallback_sqs_url = "";
	
	/**
	 * @var \Closure|null
	 */
	protected $error_function = null;
	
	/**
	 * 完遂保証するクエリ
	 * @var DynamoDBFallbackableUpdateItemCommon[]
	 */
	protected $queries = [];
	
	/**
	 * エラークエリ
	 * @var DynamoDBFallbackableUpdateItemCommon
	 */
	protected $error_query = null;
	
	/**
	 * DynamoDBCompletionGuaranteeAndFallback constructor.
	 * @param SqsClient $sqs_client
	 * @param $fallback_sqs_url
	 * @param \Closure $error_function
	 */
	public function __construct($sqs_client, $fallback_sqs_url, $error_function = null)
	{
		$this->sqs_client = $sqs_client;
		$this->fallback_sqs_url = $fallback_sqs_url;
		
		if (!is_null($error_function)) {
			$this->error_function = $error_function;
		}
	}
	
	/**
	 * クエリ
	 * @param DynamoDBFallbackableUpdateItemCommon $query
	 */
	public function addQuery($query)
	{
		$this->queries[] = clone $query;
	}
	
	/**
	 * Serial実行
	 * @throws DynamoDB_Exception
	 */
	public function execSerial()
	{
		//実行IDを生成
		$executor_id = Uuid::uuid4()->getHex();
		
		//クエリをコピー
		$queries = $this->queries;
		
		//チェック
		$appended_keys = [];
		foreach ($queries as $query) {
			
			$key = $query->getKey();
			ksort($key);
			
			$appended_key = $query->getTableName() . serialize($key);
			
			//同じ行を同時に2回更新してたらエラーとする
			if (isset($appended_keys[$appended_key])) {
				$this->error_query = $query;
				throw new DynamoDB_RequestException("1行の更新を複数に分けないでください。");
			}
			
			//ある
			$appended_keys[$appended_key] = true;
		}
		unset($appended_keys);
		
		//実行IDを各アイテムに付与していく
		$num = 0;
		try {
			foreach ($queries as $query) {
				
				//更新時
				if ($query instanceof DynamoDBUpdateItemBuilder) {
					
					//実行IDを付与する
					$resp = $query->getDynamoDBBuilder()
						->tableBuilder($query->getTableName())
						->updateItem()
						->setKey($query->getKey())
						->addUpdateAppendSetList("_executor", $executor_id)
						->exec();
					
					//チェック
					if (!$resp) {
						
						$this->error_query = $query;
						
						//付与失敗
						throw new DynamoDB_Exception("サーバの負荷が高いため、更新に失敗しました。");
					}
					
					//実行IDがあれば動かす
					$query->addConditionContain("_executor", $executor_id);
					
					//実行したら実行IDを除去
					$query->addUpdateDeleteSetList("_executor", $executor_id);
				}
				//追加時
				else if ($query instanceof DynamoDBPutItemBuilder) {
					
					//既にある場合は追加しない
					if(!$query->isOverride()){
						$query->addConditionAttributeNotExist($query->getPartitionKeyName());
					}
				}
				//削除
				else if ($query instanceof DynamoDBDeleteItemBuilder) {
					
					//もう無い場合は削除しない
					$query->addConditionAttributeExist($query->getPartitionKeyName());
				}
				
				$num++;
			}
		}
		catch (DynamoDB_Exception $e) {
			
			//失敗したので実行IDは除去
			for ($i = 0; $i < $num; $i++) {
				
				//実行IDを除去する
				$query = $queries[$i];
				if ($query instanceof DynamoDBUpdateItemBuilder) {
					
					$query->getDynamoDBBuilder()
						->tableBuilder($query->getTableName())
						->updateItem()
						->setKey($query->getKey())
						->addUpdateReplaceSetList("_executor", $executor_id)
						->exec();
				}
				
			}
			
			throw $e;
		}
		
		//-------------------
		// 実行していく
		//-------------------
		while (count($queries) > 0) {
			
			//先頭から1つ取得
			$query = array_shift($queries);
			
			try {
				//クエリ発行
				if (!$query->exec()) {
					
					//AWSのエラー
					$ex = $query->getException();
					if (!is_null($ex)) {
						throw $ex;
					}
					throw new DynamoDB_Exception("エラーが発生しました", 0);
				}
			}
			catch (DynamoDB_Exception $e) {
				$this->error_query = $query;
				
				//ログに残す
				$this->addLog($executor_id, $this->queries, [$query], "フォールバック不可");
				
				//これは完全に失敗
				throw $e;
			}
			catch (DynamoDbException $e) {
				$this->error_query = $query;
				
				try {
					//物によってはフォールバックする
					switch ($e->getAwsErrorCode()) {
						case DynamoDBErrorCode::PROVISIONED_THROUGHPUT_EXCEEDED_EXCEPTION:
						case DynamoDBErrorCode::ITEM_COLLECTION_SIZE_LIMIT_EXCEEDED_EXCEPTION:
						case DynamoDBErrorCode::LIMIT_EXCEEDED_EXCEPTION:
						case DynamoDBErrorCode::THROTTLING_EXCEPTION:
						case DynamoDBErrorCode::UNRECOGNIZED_CLIENT_EXCEPTION:
						case DynamoDBErrorCode::INTERNAL_SERVER_ERROR:
							$this->fallbackEnqueue($executor_id, $queries);
							return false;    //成功はした
					}
				}
				catch (DynamoDB_FallbackException $ex) {
					//フォールバックにも失敗したのでログに残す
					$this->addLog($executor_id, $this->queries, [$query], "フォールバック失敗");
				}
				
				throw new DynamoDB_Exception("エラーが発生しました", 0, $e);
			}
		}
		
		return true;
	}
	
	
	/**
	 * 実行
	 * @throws DynamoDB_Exception
	 */
	public function exec()
	{
		//クエリをコピー
		$queries = $this->queries;
		
		//----------------------------------
		// クエリのチェック
		//----------------------------------
		//チェック
		$appended_keys = [];
		foreach ($queries as $query) {
			
			$key = $query->getKey();
			ksort($key);
			
			$appended_key = $query->getTableName() . serialize($key);
			
			//同じ行を同時に2回更新してたらエラーとする
			if (isset($appended_keys[$appended_key])) {
				$this->error_query = $query;
				throw new DynamoDB_RequestException("1行の更新を複数に分けないでください。");
			}
			
			//ある
			$appended_keys[$appended_key] = true;
		}
		unset($appended_keys);
		
		//----------------------------------
		// 実行IDを付与
		//----------------------------------
		
		//実行IDを生成
		$executor_id = time()."-".Uuid::uuid4()->getHex();
		
		//実行IDを各アイテムに付与していく
		/**
		 * @var DynamoDBFallbackableUpdateItemCommon[] $add_id_queries
		 */
		$add_id_queries = [];
		$add_id_promises = [];
		foreach ($queries as $query) {
			
			//更新時
			if ($query instanceof DynamoDBUpdateItemBuilder) {
				
				//実行IDを付与する
				$add_id_query = $query->getDynamoDBBuilder()
					->tableBuilder($query->getTableName())
					->updateItem()
					->setKey($query->getKey())
					->addUpdateAppendSetList("_executor", $executor_id);
				
				$add_id_queries[] = $add_id_query;
				$add_id_promises[] = $add_id_query->execAsync();
				
				//以下は実クエリの変更
				
				//実行IDがあれば動かす
				$query->addConditionContain("_executor", $executor_id);
				
				//実行したら実行IDを除去
				$query->addUpdateDeleteSetList("_executor", $executor_id);
			}
			//追加時
			else if ($query instanceof DynamoDBPutItemBuilder) {
				
				//既にある場合は追加しない
				if(!$query->isOverride()){
					$query->addConditionAttributeNotExist($query->getPartitionKeyName());
				}
			}
			//削除
			else if ($query instanceof DynamoDBDeleteItemBuilder) {
				
				//もう無い場合は削除しない
				$query->addConditionAttributeExist($query->getPartitionKeyName());
			}
		}
		
		//クエリを一旦実行します
		try {
			
			//100件ずつに分割する
			$list = array_chunk($add_id_promises, 100);
			foreach ($list as $action_promises) {
				all($action_promises)->wait();
			}
			foreach ($add_id_queries as $add_id_query) {
				if ($add_id_query->getResult() === false) {
					throw new DynamoDB_Exception("IDの付与に失敗しました");
				}
			}
			
		}
		catch (DynamoDB_Exception $e) {
			
			//失敗したので実行IDは除去
			$remove_promises = [];
			foreach ($add_id_queries as $query) {
				
				//成功したやつは除去していく
				if ($query->getResult() === true && $query instanceof DynamoDBUpdateItemBuilder) {
					$remove_promises[] = $query->getDynamoDBBuilder()
						->tableBuilder($query->getTableName())
						->updateItem()
						->setKey($query->getKey())
						->addUpdateDeleteSetList("_executor", $executor_id)
						->execAsync();
				}
			}
			
			try {
				
				/**
				 * これミスったら流石に諦める
				 * @var DynamoDBFallbackableUpdateItemCommon[] $remove_queries
				 */
				$remove_queries = all($remove_promises)->wait();
				
				foreach ($remove_queries as $remove_query) {
					if ($remove_query->getResult() === false) {
						throw new DynamoDB_Exception("IDの除去に失敗しました");
					}
				}
				
			}
			catch (DynamoDB_Exception $e) {
				$this->addLog($executor_id, $this->queries, [], "フォールバック不可");
			}
			
			throw $e;
		}
		
		unset($add_id_queries);
		unset($add_id_promises);
		
		
		
		//----------------------------------
		// パラレル送信する
		//----------------------------------
		$action_promises = [];
		foreach ($queries as $query) {
			$action_promises[] = $query->execAsync();
		}
		
		//クエリパラレル実行
		try {
			//100件ずつに分割する
			$list = array_chunk($action_promises, 100);
			foreach ($list as $promises){
				all($promises)->wait();
			}
			
			foreach ($queries as $query) {
				if ($query->getResult() === false) {
					throw new DynamoDB_Exception("実行に失敗しました");
				}
			}
		}
		catch (DynamoDB_Exception $e) {
			
			//失敗したやつのエラーを見て再試行するか決定する
			$can_fallback = true;
			
			$error_queries = [];
			foreach ($queries as $query){
				if ($query->getResult() === false) {
					$error_queries[] = $query;
					
					//物によってはフォールバックする
					switch ($query->getException()->getAwsErrorCode()) {
						case DynamoDBErrorCode::PROVISIONED_THROUGHPUT_EXCEEDED_EXCEPTION:
						case DynamoDBErrorCode::ITEM_COLLECTION_SIZE_LIMIT_EXCEEDED_EXCEPTION:
						case DynamoDBErrorCode::LIMIT_EXCEEDED_EXCEPTION:
						case DynamoDBErrorCode::THROTTLING_EXCEPTION:
						case DynamoDBErrorCode::UNRECOGNIZED_CLIENT_EXCEPTION:
						case DynamoDBErrorCode::INTERNAL_SERVER_ERROR:
							//↑のエラーはフォールバックOK
							break;
						default:
							$can_fallback = false;
					}
				}
			}
			
			//-----------------------------
			// フォールバック
			//-----------------------------
			if($can_fallback){
				
				//フォールバック出来るのでSQSへ
				try {
					$this->fallbackEnqueue($executor_id, $queries);
					return true;
				}
				catch (DynamoDB_FallbackException $ex) {
					//フォールバックにも失敗したのでログに残す
					$this->addLog($executor_id, $this->queries, $error_queries, "フォールバック失敗");
				}
			}
			else{
				//フォールバック出来ない
				
				//ログに残す
				$this->addLog($executor_id, $this->queries, $error_queries, "フォールバック不可");
			}
			
			throw $e;
		}
		
		return true;
	}
	
	
	/**
	 * フォールバックキューに登録する
	 * @param $executor_id
	 * @param DynamoDBFallbackableUpdateItemCommon[] $queries
	 * @return bool
	 * @throws DynamoDB_FallbackException
	 */
	protected function fallbackEnqueue($executor_id, $queries)
	{
		try {
			
			//メッセージボディの作成
			$message_body = [
				"executor_id" => $executor_id,
				"queries" => []
			];
			
			foreach ($queries as $query) {
				$message_body["queries"][] = [
					"command" => $query->getCommand(),
					"table" => $query->getTableName(),
					"request_params" => $query->getRequestParams()
				];
			}
			
			//フォールバックとしてSQSに投げる
			$this->sqs_client->sendMessage([
				"QueueUrl" => $this->fallback_sqs_url,
				"MessageBody" => json_encode($message_body)
			]);
		}
		catch (SqsException $e) {
			throw new DynamoDB_FallbackException("SQSへのFallbackに失敗しました", 1, $e);
		}
		
		return true;
	}
	
	/**
	 * ログへ
	 * @param $executor_id
	 * @param DynamoDBFallbackableUpdateItemCommon[] $queries
	 * @param DynamoDBFallbackableUpdateItemCommon[] $error_queries
	 * @param $message
	 */
	protected function addLog($executor_id, $queries, $error_queries, $message)
	{
		if (!is_null($this->error_function)) {
			$func = $this->error_function;
			$func($executor_id, $queries, $error_queries, $message);
		}
	}
}