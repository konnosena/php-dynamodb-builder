<?php
namespace konnosena\DynamoDB\Command;

use Aws\DynamoDb\Exception\DynamoDbException;
use GuzzleHttp\Promise\Promise;
use konnosena\DynamoDB\Common\DynamoDBGlobalCommandCommon;


/**
 * DynamoDBDescribeLimitsBuilder
 * 現在のプロビジョニング制限を取得します
 */
class DynamoDBDescribeLimitsBuilder extends DynamoDBGlobalCommandCommon
{
	const ID = "L804";
	const COMMAND_NAME = "DescribeLimits";
	
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
	public function createRequestParams(){
	
		return [];
	}
	
	/**
	 * リクエスト処理
	 * @param $request_params
	 * @return Promise
	 */
	protected function requestMainAsync($request_params)
	{
		return $this->_dynamodb->describeLimitsAsync($request_params);
	}
	
	/**
	 * Query発行
	 * @return array
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
	protected function success($response){
		return [
			'AccountMaxReadCapacityUnits' => $response["AccountMaxReadCapacityUnits"] ?? 0,
			'AccountMaxWriteCapacityUnits' => $response["AccountMaxWriteCapacityUnits"] ?? 0,
			'TableMaxReadCapacityUnits' => $response["TableMaxReadCapacityUnits"] ?? 0,
			'TableMaxWriteCapacityUnits' => $response["TableMaxWriteCapacityUnits"] ?? 0,
		];
	}
	
	/**
	 * リクエスト失敗
	 * @param DynamoDbException $e
	 * @return array
	 */
	protected function failed($e){
		return [];
	}
	
}

