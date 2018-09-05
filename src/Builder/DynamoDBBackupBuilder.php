<?php

namespace konnosena\DynamoDB\Builder;

use konnosena\DynamoDB\Command\DynamoDBCreateBackupBuilder;
use konnosena\DynamoDB\Command\DynamoDBDeleteBackupBuilder;
use konnosena\DynamoDB\Command\DynamoDBDescribeBackupBuilder;
use konnosena\DynamoDB\Command\DynamoDBListBackupsBuilder;
use konnosena\DynamoDB\Command\DynamoDBRestoreTableFromBackupBuilder;

/**
 * DynamoDBのバックアップ系ビルダを整理する起点
 */
class DynamoDBBackupBuilder
{
	const ID = "L804";
	
	private $_dynamodb = null;
	
	/**
	 * コンストラクタ
	 * @param $dynamodb
	 */
	public function __construct($dynamodb)
	{
		$this->_dynamodb = $dynamodb;
	}
	
	//-----------------------------------------------------------
	// バックアップビルダ系
	//-----------------------------------------------------------
	
	/**
	 * バックアップ作成
	 * @param $table
	 * @param $backup_name
	 * @return DynamoDBCreateBackupBuilder
	 */
	public function create($table, $backup_name)
	{
		return new DynamoDBCreateBackupBuilder($this->_dynamodb, $table, $backup_name);
	}


	/**
	 * バックアップ削除
	 * @param string $backup_arn
	 * @return DynamoDBDeleteBackupBuilder
	 */
	public function delete($backup_arn)
	{
		return new DynamoDBDeleteBackupBuilder($this->_dynamodb, $backup_arn);
	}
	
	
	/**
	 * バックアップ一覧取得
	 * @return DynamoDBListBackupsBuilder
	 */
	public function list()
	{
		return new DynamoDBListBackupsBuilder($this->_dynamodb);
	}
	
	/**
	 * バックアップ情報取得
	 * @param $table
	 * @return DynamoDBDescribeBackupBuilder
	 */
	public function info($table)
	{
		return new DynamoDBDescribeBackupBuilder($this->_dynamodb, $table);
	}
	
	/**
	 * 復元する
	 * @param $table
	 * @param $backup_arn
	 * @return DynamoDBRestoreTableFromBackupBuilder
	 */
	public function restore($table, $backup_arn)
	{
		return new DynamoDBRestoreTableFromBackupBuilder($this->_dynamodb, $table, $backup_arn);
	}
	

}
