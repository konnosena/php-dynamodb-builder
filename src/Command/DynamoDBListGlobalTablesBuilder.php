<?php

namespace konnosena\DynamoDB\Command;

use Aws\DynamoDb\Exception\DynamoDbException;
use function GuzzleHttp\Promise\coroutine;
use GuzzleHttp\Promise\PromiseInterface;
use konnosena\DynamoDB\Common\DynamoDBGlobalCommandCommon;


/**
 * DynamoDBListGlobalTablesBuilder
 * グローバルテーブル情報一覧
 */
class DynamoDBListGlobalTablesBuilder extends DynamoDBGlobalCommandCommon
{
	const ID = "L804";
	const COMMAND_NAME = "ListGlobalTables";
	
	protected $_region_name = "";
	
	//----------------------------------------------------------------------------------
	// クエリ発行前
	//----------------------------------------------------------------------------------
	
	/**
	 * 指定リージョンのみに制限する
	 * @param string $region_name
	 * @return $this
	 */
	public function setRegionName($region_name)
	{
		$this->_region_name = $region_name;
		return $this;
	}
	
	//----------------------------------------------------------------------------------
	// クエリ発行
	//----------------------------------------------------------------------------------
	/**
	 * リクエストの作成
	 * @return array
	 */
	public function createRequestParams()
	{
		
		$request_params = [];
		
		if (!empty($this->_region_name)) {
			$request_params["RegionName"] = $this->_region_name;
		}
		
		return $request_params;
	}
	
	/**
	 * リクエスト処理
	 * @param $request_params
	 * @return PromiseInterface
	 */
	protected function requestMainAsync($request_params)
	{
		return coroutine(function () use ($request_params) {
			
			$response = [];
			
			try {
				do {
					//クエリ発行
					$result = (yield $this->_dynamodb->listGlobalTables($request_params));
					
					//結果を一旦解析する
					$this->afterRequest($result);
					
					//GlobalTables
					if (!empty($result["GlobalTables"])) {
						$response = array_merge($response, $result["GlobalTables"]);
					}
					
					//ページネーションの処理
					$request_params['ExclusiveStartGlobalTableName'] = $result['LastEvaluatedGlobalTableName'];
					
				} while ($request_params['ExclusiveStartGlobalTableName']);
			}
			catch (DynamoDbException $e) {
				yield $e;
			}
			
			yield $response;
		});
	}
	
	/**
	 * Query発行
	 * @return bool|array
	 */
	public function exec()
	{
		return parent::exec();
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
	 */
	protected function failed($e)
	{
		return false;
	}
}

