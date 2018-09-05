<?php

namespace konnosena\DynamoDB\Command;

use Aws\DynamoDb\Exception\DynamoDbException;
use GuzzleHttp\Promise\Promise;
use konnosena\DynamoDB\Common\DynamoDBTableCommandCommon;
use konnosena\DynamoDB\Exception\DynamoDB_RequestException;
use konnosena\DynamoDB\Response\DynamoDBResponseTableDescriptionTrait;


/**
 * DynamoDBDeleteTableBuilder
 * テーブル削除
 */
class DynamoDBDeleteTableBuilder extends DynamoDBTableCommandCommon
{
	use DynamoDBResponseTableDescriptionTrait;
	
	const ID = "L804";
	const COMMAND_NAME = "DeleteTable";
	
	
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
		return $this->_dynamodb->deleteTableAsync($request_params);
	}
	
	/**
	 * Query発行
	 * @return bool
	 */
	public function exec()
	{
		return parent::exec();
	}
	/**
	 * リクエスト成功
	 * @param $response
	 * @return bool
	 */
	protected function success($response){
		return true;
	}
	
	/**
	 * リクエスト失敗
	 * @param DynamoDbException $e
	 * @return bool
	 * @throws DynamoDB_RequestException
	 */
	protected function failed($e){
		
		switch ($e->getAwsErrorCode()){
			case "ResourceInUseException":
				throw new DynamoDB_RequestException("現在作業中のテーブルです", 0, $e);
			case "ResourceNotFoundException":
				throw new DynamoDB_RequestException("テーブルが見つかりません");
		}
		
		return false;
	}
}

