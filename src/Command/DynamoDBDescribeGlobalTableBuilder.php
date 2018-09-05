<?php
namespace konnosena\DynamoDB\Command;

use Aws\DynamoDb\Exception\DynamoDbException;
use konnosena\DynamoDB\Common\DynamoDBGlobalTableCommandCommon;
use konnosena\DynamoDB\Response\DynamoDBResponseGlobalTableDescriptionTrait;


/**
 * DynamoDBDescribeGlobalTableBuilder
 * Globalテーブルの情報取得
 */
class DynamoDBDescribeGlobalTableBuilder extends DynamoDBGlobalTableCommandCommon
{
	use DynamoDBResponseGlobalTableDescriptionTrait;
	
	const ID = "L804";
	const COMMAND_NAME = "DescribeGlobalTable";
	
	//----------------------------------------------------------------------------------
	// クエリ発行
	//----------------------------------------------------------------------------------
	
	/**
	 * リクエストの作成
	 * @return array
	 */
	public function createRequestParams(){
	
		return [
			"GlobalTableName" => $this->_global_table_name
		];
	}

	/**
	 * リクエスト部分の実装
	 * @param $request_params
	 * @return \GuzzleHttp\Promise\Promise
	 */
	protected function requestMainAsync($request_params)
	{
		//クエリ発行
		return $this->_dynamodb->describeGlobalTableAsync($request_params);
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
		return $this->getResponseGlobalTableDescription();
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

