<?php
namespace konnosena\DynamoDB\Command;

use Aws\DynamoDb\Exception\DynamoDbException;
use GuzzleHttp\Promise\Promise;
use konnosena\DynamoDB\Common\DynamoDBTableCommandCommon;
use konnosena\DynamoDB\Exception\DynamoDB_RequestException;
use konnosena\DynamoDB\Response\DynamoDBResponseContinuousBackupDescriptionTrait;


/**
 * DynamoDBDescribeContinuousBackupsBuilder
 * テーブルのバックアップ設定の取得
 */
class DynamoDBDescribeContinuousBackupsBuilder extends DynamoDBTableCommandCommon
{
	use DynamoDBResponseContinuousBackupDescriptionTrait;
	
	const ID = "L804";
	const COMMAND_NAME = "DescribeContinuousBackups";
	
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
		return $this->_dynamodb->describeContinuousBackupsAsync($request_params);
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
		return $this->getResponseIsContinuousBackups();
	}
	
	/**
	 * リクエスト失敗
	 * @param DynamoDbException $e
	 * @return bool
	 * @throws DynamoDB_RequestException
	 */
	protected function failed($e){
		
		switch ($e->getAwsErrorCode()){
			case "TableNotFoundException":
				throw new DynamoDB_RequestException("テーブルが存在しません");
		}
		
		return false;
	}
	
}

