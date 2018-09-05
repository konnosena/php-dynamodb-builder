<?php
namespace konnosena\DynamoDB\Command;

use Aws\DynamoDb\Exception\DynamoDbException;
use GuzzleHttp\Promise\Promise;
use konnosena\DynamoDB\Common\DynamoDBTableCommandCommon;
use konnosena\DynamoDB\Response\DynamoDBResponseTimeToLiveDescriptionTrait;


/**
 * DynamoDBDescribeTimeToLiveBuilder
 * テーブル情報取得
 */
class DynamoDBDescribeTimeToLiveBuilder extends DynamoDBTableCommandCommon
{
	use DynamoDBResponseTimeToLiveDescriptionTrait;
	
	const ID = "L804";
	const COMMAND_NAME = "DescribeTimeToLive";
	
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
	
		return [
			"TableName" => $this->_table_name
		];
	}
	
	/**
	 * リクエスト処理
	 * @param $request_params
	 * @return Promise
	 */
	protected function requestMainAsync($request_params)
	{
		return $this->_dynamodb->describeTimeToLiveAsync($request_params);
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
		return $this->getResponseTimeToLiveDescription();
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

