<?php

namespace konnosena\DynamoDB\Command;

use Aws\DynamoDb\Exception\DynamoDbException;
use GuzzleHttp\Promise\Promise;
use konnosena\DynamoDB\Common\DynamoDBGlobalCommandCommon;
use konnosena\DynamoDB\DynamoDBBuilder;
use konnosena\DynamoDB\Response\DynamoDBResponseBackupDescriptionTrait;


/**
 * DynamoDBDescribeBackupBuilder
 * バックアップ情報の取得
 */
class DynamoDBDescribeBackupBuilder extends DynamoDBGlobalCommandCommon
{
	use DynamoDBResponseBackupDescriptionTrait;
	
	const ID = "L804";
	const COMMAND_NAME = "DescribeBackup";
	
	//バックアップ名
	protected $_backup_arn = "";
	
	//----------------------------------------------------------------------------------
	// クエリ発行前
	//----------------------------------------------------------------------------------
	/**
	 * DynamoDBQuery constructor.
	 * @param DynamoDBBuilder $dynamodb
	 * @param string $backup_arn
	 */
	public function __construct(DynamoDBBuilder $dynamodb, $backup_arn)
	{
		parent::__construct($dynamodb);
		
		$this->_backup_arn = $backup_arn;
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
		
		return [
			"BackupArn" => $this->_backup_arn
		];
	}
	
	/**
	 * リクエスト処理
	 * @param $request_params
	 * @return Promise
	 */
	protected function requestMainAsync($request_params)
	{
		return $this->_dynamodb->describeBackupAsync($request_params);
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
		return $this->getResponseBackupDescription();
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

