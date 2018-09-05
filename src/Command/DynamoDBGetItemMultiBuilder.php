<?php

namespace konnosena\DynamoDB\Command;

use Aws\DynamoDb\Exception\DynamoDbException;
use Aws\Result;
use function GuzzleHttp\Promise\coroutine;
use GuzzleHttp\Promise\PromiseInterface;
use konnosena\DynamoDB\Common\DynamoDBBackoffRetryTrait;
use konnosena\DynamoDB\Common\DynamoDBGlobalCommandCommon;
use konnosena\DynamoDB\Exception\DynamoDB_Exception;
use konnosena\DynamoDB\Exception\DynamoDB_RequestException;
use konnosena\DynamoDB\Option\DynamoDBOptionConsumedCapacityTrait;


/**
 * DynamoDBのクエリ組み立て
 * @property DynamoDBGetItemBuilder[] $get_commands
 */
class DynamoDBGetItemMultiBuilder extends DynamoDBGlobalCommandCommon
{
	use DynamoDBOptionConsumedCapacityTrait;
	use DynamoDBBackoffRetryTrait;
	
	const ID = "L804";
	const COMMAND_NAME = "GetItemMulti";
	
	//特殊なのでTraitは使わない
	protected $_keys = [];
	protected $_columns = [];
	protected $_consistent_reads = [];
	
	/**
	 * キーを追加
	 * @param $table
	 * @param $key
	 * @return $this
	 */
	public function addKey($table, $key)
	{
		$this->_keys[$table][] = $key;
		return $this;
	}
	
	/**
	 * キーを追加
	 * @param $table
	 * @param $keys
	 * @return $this
	 */
	public function addKeys($table, $keys)
	{
		if (empty($this->_keys[$table])) {
			$this->setKeys($table, $keys);
		}
		else {
			$this->_keys[$table] = array_merge($this->_keys[$table], $keys);
		}
		
		
		return $this;
	}
	
	/**
	 * キーを追加
	 * @param $table
	 * @param array $keys
	 * @return $this
	 */
	public function setKeys($table, $keys)
	{
		$this->_keys[$table] = $keys;
		return $this;
	}
	
	/**
	 * 取得カラムの設定
	 * @param $table
	 * @param array|string $column_names
	 * @return $this
	 */
	public function addColumn($table, $column_names)
	{
		if (!isset($this->_columns[$table])) {
			$this->_columns[$table] = [];
		}
		
		if (!is_array($column_names)) {
			$column_names[] = $column_names;
		}
		
		$this->_columns[$table] = array_merge($this->_columns[$table], $column_names);
		
		return $this;
	}
	
	/**
	 * 強い整合性の読み込み
	 * @param $table
	 * @param bool $bool
	 * @return $this
	 */
	public function isConsistentRead($table, $bool)
	{
		$this->_consistent_reads[$table] = $bool;
		return $this;
	}
	
	
	//----------------------------------------------------------------------------------
	// クエリ発行
	//----------------------------------------------------------------------------------
	
	/**
	 * リクエストの作成
	 * @return array|mixed
	 */
	public function createRequestParams()
	{
		$request_items_stack = [];
		
		//テーブルごとに処理していく
		foreach ($this->_keys as $table => $keys) {
			
			//逆順にスタック
			for ($i = count($keys) - 1; $i >= 0; $i--) {
				$request_items_stack[] = [
					"TableName" => $table,
					"Key" => $this->_marshaler->marshalItem($keys[$i])
				];
			}
		}
		
		//必要な情報をまとめる
		return [
			"RequestItemsStack" => $request_items_stack,
			"Columns" => $this->_columns,
			"ConsistentReads" => $this->_consistent_reads,
			"Options" => $this->_options
		];
		
	}
	
	/**
	 * リクエストを投げる
	 * @param $requests_params
	 * @return PromiseInterface
	 */
	protected function requestMainAsync($requests_params)
	{
		return coroutine(function () use ($requests_params) {
			
			//パラメータを取得
			$request_items_stack = $requests_params["RequestItemsStack"];
			$columns = $requests_params["Columns"];
			$consistent_reads = $requests_params["ConsistentReads"];
			$options = $requests_params["Options"];
			
			//-------------------
			// 実行
			//-------------------
			$response = [];
			$this->_request_params = [];
			$all_response = [];
			
			//リクエストパラメータ
			$request_params = [];
			
			try {
				
				do {
					
					//100件しか同時に出来ない(後ろから使う)
					$request_items_100 = array_splice($request_items_stack, max(count($request_items_stack) - 100, 0), 100);
					
					//構築する
					$request_items = [];
					for ($i = count($request_items_100) - 1; $i >= 0; $i--) {
						$request_item = $request_items_100[$i];
						$request_items[$request_item["TableName"]]["Keys"][] = $request_item["Key"];
					}
					
					//パラメータをつける
					foreach ($request_items as $table => &$request_item) {
						
						//取得カラム指定があれば（SELECT）
						if (!empty($columns[$table])) {
							$request_item['ProjectionExpression'] = implode(",", $columns[$table]);
						}
						
						//強い整合性の取得
						if (!empty($consistent_reads[$table])) {
							$request_item['ConsistentRead'] = $consistent_reads[$table];
						}
					}
					
					
					unset($request_item);
					
					//構築する
					$request_params = [
						"RequestItems" => $request_items
					];
					
					//オプションをくっつける
					$request_params = array_merge($request_params, $options);
					
					//オブジェクトに記憶する
					$this->_request_params[] = $request_params;
					
					//-------------------
					// リクエストの発行
					//-------------------
					$result = (yield $this->_dynamodb->batchGetItem($request_params));
					
					//レスポンス解析
					$all_response[] = $result;
					
					//-------------------
					// リクエストの解析
					//-------------------
					//レスポンス取得
					foreach ($result["Responses"] as $table => $items) {
						
						//テーブルごとにまとめる
						foreach ($items as $item) {
							$response[$table][] = $this->_marshaler->unmarshalItem($item);
						}
					}
					
					//処理が出来なかったものはもう一度取得できるようにする
					if (!empty($result["UnprocessedKeys"])) {
						
						$is_retry = false;
						
						//処理が出来なかったものはもう一度取得できるようにする
						if (!empty($result["UnprocessedKeys"])) {
							
							//取得できなかったやつは再度取得することにする
							foreach ($result["UnprocessedKeys"] as $table => $temp_unprocessed_keys) {
								
								$temp_unprocessed_keys = $temp_unprocessed_keys["Keys"];
								
								//スタックに戻す
								for ($i = count($temp_unprocessed_keys) - 1; $i >= 0; $i--) {
									$request_items_stack[] = [
										"TableName" => $table,
										"Key" => $temp_unprocessed_keys[$i]
									];
								}
							}
							
							//リトライする
							$is_retry = true;
						}
						
						//指数バックオフでリトライする
						if ($is_retry && !$this->backOffRetry()) {
							throw new DynamoDB_Exception("データベースが高負荷です。時間を置いてアクセスしてください。");
						}
						
					}
					
					
				} while (!empty($request_items_stack));
				
				yield $all_response;
			}
			catch (DynamoDB_Exception $e) {
				$command = $this->_dynamodb->getCommand("batchGetItem", $request_params);
				yield new DynamoDbException("データベースが高負荷です。時間を置いてアクセスしてください。", $command, [
					"errorCode" => "ProvisionedThroughputExceededException"
				]);
			}
			catch (DynamoDbException $e) {
				yield $e;
			}
			
		});
	}
	
	
	/**
	 * バリデーション
	 * @throws DynamoDB_RequestException
	 */
	public function validation()
	{
		if (empty($this->_keys)) {
			throw new DynamoDB_RequestException("キーが指定されていません");
		}
	}
	
	/**
	 * Query発行
	 * @return array
	 * @throws DynamoDB_RequestException
	 */
	public function exec()
	{
		$this->validation();
		return parent::exec();
	}
	
	/**
	 * Query発行
	 * @return PromiseInterface
	 * @throws DynamoDB_RequestException
	 */
	public function execAsync()
	{
		$this->validation();
		return parent::execAsync();
	}
	
	
	/**
	 * リクエスト成功
	 * @param $responses
	 * @return array
	 */
	protected function success($responses)
	{
		//パースしていく
		$results = [];
		foreach ($responses as $response) {
			foreach ($response["Responses"] as $table => $rows) {
				foreach ($rows as $row) {
					$results[] = $this->_marshaler->unmarshalItem($row);
				}
			}
		}
		return $results;
	}
	
	/**
	 * リクエスト失敗
	 * @param DynamoDbException $e
	 * @return bool
	 * @throws DynamoDB_RequestException
	 */
	protected function failed($e)
	{
		switch ($e->getAwsErrorCode()) {
			case "ResourceNotFoundException":
				throw new DynamoDB_RequestException("テーブルが存在しません");
		}
		
		return false;
	}
	
	
}
