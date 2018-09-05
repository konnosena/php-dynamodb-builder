<?php

namespace konnosena\DynamoDB\Command;

use GuzzleHttp\Promise\PromiseInterface;
use konnosena\DynamoDB\Common\DynamoDBTableCommandCommon;
use konnosena\DynamoDB\DynamoDBBuilder;
use Aws\DynamoDb\Exception\DynamoDbException;
use konnosena\DynamoDB\Response\DynamoDBResponseTableDescriptionTrait;


/**
 * DynamoDBRestoreTableFromBackupBuilder
 * バックアップからテーブルを作成
 */
class DynamoDBRestoreTableFromBackupBuilder extends DynamoDBTableCommandCommon
{
	use DynamoDBResponseTableDescriptionTrait;
	
	const ID = "L804";
	const COMMAND_NAME = "RestoreTableFromBackup";
	
	//作成テーブル名
	protected $_table_name = "";
	
	//バックアップ名
	protected $_backup_arn = "";
	
	/**
	 * コマンド名取得
	 * @return string
	 */
	public function getCommand()
	{
		return "RestoreTable";
	}
	
	//----------------------------------------------------------------------------------
	// クエリ発行前
	//----------------------------------------------------------------------------------
	/**
	 * DynamoDBQuery constructor.
	 * @param DynamoDBBuilder $dynamodb
	 * @param string $backup_arn
	 * @param $table_name
	 */
	public function __construct(DynamoDBBuilder $dynamodb, $table_name, $backup_arn)
	{
		parent::__construct($dynamodb, $table_name);
		
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
			"TargetTableName" => $this->_table_name,
			"BackupArn" => $this->_backup_arn
		];
	}
	
	/**
	 * リクエスト処理
	 * @param $request_params
	 * @return PromiseInterface
	 */
	protected function requestMainAsync($request_params)
	{
		return $this->_dynamodb->restoreTableFromBackupAsync($request_params);
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
	 */
	protected function failed($e){
		return false;
	}
	
}

