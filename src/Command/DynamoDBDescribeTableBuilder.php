<?php
namespace konnosena\DynamoDB\Command;

use Aws\DynamoDb\Exception\DynamoDbException;
use GuzzleHttp\Promise\Promise;
use konnosena\DynamoDB\Common\DynamoDBTableCommandCommon;
use konnosena\DynamoDB\Response\DynamoDBResponseTableDescriptionTrait;


/**
 * DynamoDBDescribeTableBuilder
 * テーブル情報取得
 */
class DynamoDBDescribeTableBuilder extends DynamoDBTableCommandCommon
{
	use DynamoDBResponseTableDescriptionTrait;
	
	const ID = "L804";
	const COMMAND_NAME = "DescribeTable";
	
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
		return $this->_dynamodb->describeTableAsync($request_params);
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
		return $this->getResponseTableDescription();
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

