<?php

namespace konnosena\DynamoDB\Command;

use GuzzleHttp\Promise\Promise;
use konnosena\DynamoDB\Common\DynamoDBTableCommandCommon;
use konnosena\DynamoDB\DynamoDBBuilder;
use Aws\DynamoDb\Exception\DynamoDbException;
use konnosena\DynamoDB\Exception\DynamoDB_RequestException;
use konnosena\DynamoDB\Response\DynamoDBResponseBackupDetailsTrait;


/**
 * DynamoDBCreateBackupBuilder
 * バックアップの作成
 */
class DynamoDBCreateBackupBuilder extends DynamoDBTableCommandCommon
{
	use DynamoDBResponseBackupDetailsTrait;
	
	const ID = "L804";
	const COMMAND_NAME = "CreateBackup";
	
	//バックアップ名
	protected $_backup_name = "";
	
	
	//----------------------------------------------------------------------------------
	// クエリ発行前
	//----------------------------------------------------------------------------------
	/**
	 * DynamoDBQuery constructor.
	 * @param DynamoDBBuilder $dynamodb
	 * @param $table_name
	 * @param $backup_name
	 */
	public function __construct(DynamoDBBuilder $dynamodb, $table_name, $backup_name)
	{
		parent::__construct($dynamodb, $table_name);
		
		$this->_backup_name = $backup_name;
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
			"TableName" => $this->_table_name,
			"BackupName" => $this->_backup_name
		];
	}
	
	/**
	 * リクエスト処理
	 * @param $request_params
	 * @return Promise
	 */
	protected function requestMainAsync($request_params)
	{
		return $this->_dynamodb->createBackupAsync($request_params);
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
			case "TableNotFoundException":
				throw new DynamoDB_RequestException("テーブルが存在しません");
			case "GlobalTableAlreadyExistsException":
				throw new DynamoDB_RequestException("既に存在するグローバルテーブル名です");
				/*
			case "TableInUseException":
				throw new DynamoDB_RequestException("現在作業中のテーブルです");
			case "BackupInUseException":
				throw new DynamoDB_RequestException("現在作業中のバックアップです");
				*/
		}
		
		return false;
	}
	
	
}

