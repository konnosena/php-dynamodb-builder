<?php

namespace konnosena\DynamoDB\Command;

use Aws\DynamoDb\Exception\DynamoDbException;
use function GuzzleHttp\Promise\coroutine;
use GuzzleHttp\Promise\PromiseInterface;
use konnosena\DynamoDB\Common\DynamoDBGlobalCommandCommon;
use konnosena\DynamoDB\RequestParams\DynamoDBLimitTrait;


/**
 * DynamoDBListTablesBuilder
 * テーブル名一覧
 */
class DynamoDBListTablesBuilder extends DynamoDBGlobalCommandCommon
{
	use DynamoDBLimitTrait;
	
	const ID = "L804";
	const COMMAND_NAME = "ListTables";
	
	
	//----------------------------------------------------------------------------------
	// クエリ発行前
	//----------------------------------------------------------------------------------
	
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
		
		//limit
		if ($this->_limit > 0) {
			$request_params['Limit'] = $this->_limit;
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
					$result = (yield $this->_dynamodb->listTables($request_params));
					
					//結果を一旦解析する
					$this->afterRequest($result);
					
					//TableNames
					if (!empty($result["TableNames"])) {
						$response = array_merge($response, $result["TableNames"]);
					}
					
					//ページネーションの処理
					$request_params['ExclusiveStartTableName'] = $result['LastEvaluatedTableName'];
					
				} while ($request_params['ExclusiveStartTableName']);
				
				yield $response;
			}
			catch (DynamoDbException $e) {
				yield $e;
			}
			
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

