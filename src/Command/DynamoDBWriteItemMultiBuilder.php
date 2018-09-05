<?php

namespace konnosena\DynamoDB\Command;

use Aws\DynamoDb\Exception\DynamoDbException;
use function GuzzleHttp\Promise\coroutine;
use GuzzleHttp\Promise\PromiseInterface;
use konnosena\DynamoDB\Common\DynamoDBBackoffRetryTrait;
use konnosena\DynamoDB\Common\DynamoDBGlobalCommandCommon;
use konnosena\DynamoDB\Exception\DynamoDB_Exception;
use konnosena\DynamoDB\Exception\DynamoDB_RequestException;
use konnosena\DynamoDB\Option\DynamoDBOptionConsumedCapacityTrait;
use konnosena\DynamoDB\Option\DynamoDBOptionReturnItemCollectionMetricsTrait;


/**
 * DynamoDBの挿入、削除の一括
 * @property DynamoDBGetItemBuilder[] $get_commands
 */
class DynamoDBWriteItemMultiBuilder extends DynamoDBGlobalCommandCommon
{
	//仕組み
	use DynamoDBBackoffRetryTrait;
	
	//オプション
	use DynamoDBOptionConsumedCapacityTrait;
	use DynamoDBOptionReturnItemCollectionMetricsTrait;
	
	const ID = "L804";
	const COMMAND_NAME = "WriteItemMulti";
	
	protected $_delete_keys = [];
	protected $_puts_items = [];
	
	/**
	 * 削除キーを追加
	 * @param $table
	 * @param $key
	 * @return $this
	 */
	public function addDeleteKey($table, $key)
	{
		$this->_delete_keys[$table][] = $key;
		return $this;
	}
	
	/**
	 * 削除キーを追加
	 * @param $table
	 * @param $keys
	 * @return $this
	 */
	public function addDeleteKeys($table, $keys)
	{
		if (empty($this->_delete_keys[$table])) {
			$this->setDeleteKeys($table, $keys);
		}
		else {
			$this->_delete_keys[$table] = array_merge($this->_delete_keys[$table], $keys);
		}
		
		return $this;
	}
	
	/**
	 * 削除キーを置き換え
	 * @param $table
	 * @param array $keys
	 * @return $this
	 */
	public function setDeleteKeys($table, $keys)
	{
		$this->_delete_keys[$table] = $keys;
		return $this;
	}
	
	/**
	 * 投入アイテムを追加
	 * @param $table
	 * @param $item
	 * @return $this
	 */
	public function addPutItem($table, $item)
	{
		$this->_puts_items[$table][] = $item;
		return $this;
	}
	
	/**
	 * 削除キーを置き換え
	 * @param $table
	 * @param array $items
	 * @return $this
	 */
	public function setPutItems($table, $items)
	{
		$this->_puts_items[$table] = $items;
		return $this;
	}
	
	/**
	 * 削除キーを追加
	 * @param $table
	 * @param array $items
	 * @return $this
	 */
	public function addPutItems($table, $items)
	{
		if (empty($this->_puts_items[$table])) {
			$this->setPutItems($table, $items);
		}
		else {
			$this->_puts_items[$table] = array_merge($this->_puts_items[$table], $items);
		}
		
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
		//リクエスト
		$request_items_stack = [];
		
		//DeleteRequest
		foreach ($this->_delete_keys as $table => $items) {
			
			//逆順にスタック
			for ($i = count($items) - 1; $i >= 0; $i--) {
				$request_items_stack[] = [
					"TableName" => $table,
					"RequestItem" => [
						"DeleteRequest" => [
							"Key" => $this->_marshaler->marshalItem($items[$i])
						]
					]
				];
			}
		}
		
		//PutRequest
		foreach ($this->_puts_items as $table => $items) {
			
			//逆順にスタック
			for ($i = count($items) - 1; $i >= 0; $i--) {
				$request_items_stack[] = [
					"TableName" => $table,
					"RequestItem" => [
						"PutRequest" => [
							"Item" => $this->_marshaler->marshalItem($items[$i])
						]
					]
				];
			}
		}
		
		//必要な情報をまとめる
		return [
			"RequestItemsStack" => $request_items_stack,
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
			
			//リトライカウント
			$this->clearRetryCount();
			
			//パラメータを取得
			$request_items_stack = $requests_params["RequestItemsStack"];
			$options = $requests_params["Options"];
			
			//-------------------
			// 実行
			//-------------------
			$response = [];
			$request_params = [];
			try {
				do {
					//25件しか同時に出来ない(後ろから使う)
					$request_items = array_splice($request_items_stack, max(count($request_items_stack) - 25, 0), 25);
					
					//構築する
					$request_params = [];
					for ($i = count($request_items) - 1; $i >= 0; $i--) {
						$request_item = $request_items[$i];
						$request_params["RequestItems"][$request_item["TableName"]][] = $request_item["RequestItem"];
					}
					
					//オプションをくっつける
					$request_params = array_merge($request_params, $options);
					
					//オブジェクトに記憶する
					$this->_request_params[] = $request_params;
					
					//-------------------
					// リクエストの発行
					//-------------------
					//リクエスト
					$result = $this->_dynamodb->batchWriteItem($request_params);
					
					//レスポンス解析
					$response[] = $result;
					
					//-------------------
					// リクエストの解析
					//-------------------
					$is_retry = false;
					
					//処理が出来なかったものはもう一度取得できるようにする
					if (!empty($result["UnprocessedItems"])) {
						
						//取得できなかったやつは再度取得することにする
						foreach ($result["UnprocessedItems"] as $table => $temp_unprocessed_items) {
							//キューに戻す
							for ($i = count($temp_unprocessed_items) - 1; $i >= 0; $i--) {
								$request_items_stack[] = [
									"TableName" => $table,
									"RequestItem" => $temp_unprocessed_items[$i]
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
					
				} while (!empty($request_items_stack));
				
				yield $response;
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
		if (empty($this->_delete_keys) && empty($this->_puts_items)) {
			throw new DynamoDB_RequestException("更新内容が指定されていません");
		}
	}
	
	/**
	 * Query発行
	 * @return array|bool
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
	 * @param $response
	 * @return array
	 */
	protected function success($response)
	{
		return $response;
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
