<?php

namespace konnosena\DynamoDB\Builder;

use konnosena\DynamoDB\Command\DynamoDBCreateBackupBuilder;
use konnosena\DynamoDB\Command\DynamoDBCreateTableBuilder;
use konnosena\DynamoDB\Command\DynamoDBDeleteItemBuilder;
use konnosena\DynamoDB\Command\DynamoDBDeleteTableBuilder;
use konnosena\DynamoDB\Command\DynamoDBDescribeContinuousBackupsBuilder;
use konnosena\DynamoDB\Command\DynamoDBDescribeTableBuilder;
use konnosena\DynamoDB\Command\DynamoDBGetItemBuilder;
use konnosena\DynamoDB\Command\DynamoDBGetItemMultiForTableBuilder;
use konnosena\DynamoDB\Command\DynamoDBListBackupsBuilder;
use konnosena\DynamoDB\Command\DynamoDBListTablesBuilder;
use konnosena\DynamoDB\Command\DynamoDBListTagsOfResourceBuilder;
use konnosena\DynamoDB\Command\DynamoDBPutItemBuilder;
use konnosena\DynamoDB\Command\DynamoDBQueryBuilder;
use konnosena\DynamoDB\Command\DynamoDBScanBuilder;
use konnosena\DynamoDB\Command\DynamoDBTagResourceBuilder;
use konnosena\DynamoDB\Command\DynamoDBUnTagResourceBuilder;
use konnosena\DynamoDB\Command\DynamoDBUpdateItemBuilder;
use konnosena\DynamoDB\Command\DynamoDBCreateTableGSIBuilder;
use konnosena\DynamoDB\Command\DynamoDBDeleteTableGSIBuilder;
use konnosena\DynamoDB\Command\DynamoDBUpdateTableBuilder;
use konnosena\DynamoDB\Command\DynamoDBUpdateTableGSIBuilder;
use konnosena\DynamoDB\Command\DynamoDBUpdateTimeToLiveBuilder;
use konnosena\DynamoDB\Command\DynamoDBWriteItemMultiForTableBuilder;

/**
 * DynamoDBのテーブル系ビルダを整理する起点
 */
class DynamoDBTableBuilder
{
	const ID = "L804";
	
	private $_dynamodb = null;
	private $_table_name = null;
	private $_table_info = null;
	
	/**
	 * コンストラクタ
	 * @param $dynamodb
	 * @param $table_name
	 */
	public function __construct($dynamodb, $table_name)
	{
		$this->_dynamodb = $dynamodb;
		$this->_table_name = $table_name;
	}
	
	//-----------------------------------------------------------
	// テーブルビルダ系
	//-----------------------------------------------------------
	
	/**
	 * テーブル作成
	 * @param int $read_capacity_units
	 * @param int $write_capacity_units
	 * @return DynamoDBCreateTableBuilder
	 */
	public function createTable($read_capacity_units = 5, $write_capacity_units = 5)
	{
		return new DynamoDBCreateTableBuilder($this->_dynamodb, $this->_table_name, $read_capacity_units, $write_capacity_units);
	}

	/**
	 * テーブル更新
	 * @return DynamoDBUpdateTableBuilder
	 */
	public function updateTable()
	{
		return new DynamoDBUpdateTableBuilder($this->_dynamodb, $this->_table_name);
	}

	/**
	 * テーブル削除
	 * @return DynamoDBDeleteTableBuilder
	 */
	public function deleteTable()
	{
		return new DynamoDBDeleteTableBuilder($this->_dynamodb, $this->_table_name);
	}
	
	/**
	 * テーブル一覧取得
	 * @return DynamoDBListTablesBuilder
	 */
	public function listTable()
	{
		return new DynamoDBListTablesBuilder($this->_dynamodb);
	}
	
	/**
	 * テーブル情報取得
	 * @return DynamoDBDescribeTableBuilder
	 */
	public function tableInfo()
	{
		return new DynamoDBDescribeTableBuilder($this->_dynamodb, $this->_table_name);
	}
	
	/**
	 * 継続テーブルバックアップ情報取得
	 * @return DynamoDBDescribeContinuousBackupsBuilder
	 */
	public function continuousBackupInfo()
	{
		return new DynamoDBDescribeContinuousBackupsBuilder($this->_dynamodb, $this->_table_name);
	}

	/**
	 * バックアップ作成
	 * @param $backup_name
	 * @return DynamoDBCreateBackupBuilder
	 */
	public function createBackup($backup_name)
	{
		return new DynamoDBCreateBackupBuilder($this->_dynamodb, $this->_table_name, $backup_name);
	}
	
	
	/**
	 * 継続テーブルバックアップ情報取得
	 * @return DynamoDBDescribeContinuousBackupsBuilder
	 */
	public function backupInfo()
	{
		return new DynamoDBDescribeContinuousBackupsBuilder($this->_dynamodb, $this->_table_name);
	}
	
	
	/**
	 * バックアップ一覧取得
	 * @return DynamoDBListBackupsBuilder
	 */
	public function backupList()
	{
		return (new DynamoDBListBackupsBuilder($this->_dynamodb))->setTable($this->_table_name);
	}

	
	
	
	/**
	 * TTL情報取得
	 * @param $attribute_name
	 * @param bool $enabled_ttl
	 * @return DynamoDBUpdateTimeToLiveBuilder
	 */
	public function ttlInfo($attribute_name, $enabled_ttl = false)
	{
		return new DynamoDBUpdateTimeToLiveBuilder($this->_dynamodb, $this->_table_name, $attribute_name, $enabled_ttl);
	}
	
	/**
	 * TTL更新
	 * @param $attribute_name
	 * @param bool $enabled_ttl
	 * @return DynamoDBUpdateTimeToLiveBuilder
	 */
	public function updateTTL($attribute_name, $enabled_ttl = false)
	{
		return new DynamoDBUpdateTimeToLiveBuilder($this->_dynamodb, $this->_table_name, $attribute_name, $enabled_ttl);
	}
	
	
	//-----------------------------------------------------------
	// GlobalSecondaryIndex系
	//-----------------------------------------------------------
	
	/**
	 * テーブルGSI追加
	 * @return DynamoDBCreateTableGSIBuilder
	 */
	public function createGSI()
	{
		return new DynamoDBCreateTableGSIBuilder($this->_dynamodb, $this->_table_name);
	}

	
	/**
	 * テーブルGSI削除
	 * @return DynamoDBDeleteTableGSIBuilder
	 */
	public function deleteGSI()
	{
		return new DynamoDBDeleteTableGSIBuilder($this->_dynamodb, $this->_table_name);
	}

	
	/**
	 * テーブルGSI更新
	 * @return DynamoDBUpdateTableGSIBuilder
	 */
	public function updateGSI()
	{
		return new DynamoDBUpdateTableGSIBuilder($this->_dynamodb, $this->_table_name);
	}
	
	
	//-----------------------------------------------------------
	// Tag系
	//-----------------------------------------------------------
	
	
	/**
	 * tagを取得
	 * @return DynamoDBListTagsOfResourceBuilder
	 */
	public function listTag()
	{
		//テーブル名からは取得できないのでinfo取得
		if(empty($this->_table_info)){
			$this->_table_info = $this->tableInfo();
		}
		
		return new DynamoDBListTagsOfResourceBuilder($this->_dynamodb, $this->_table_info["TableArn"]);
	}

	
	/**
	 * tagを更新
	 * @return DynamoDBTagResourceBuilder
	 */
	public function updateTag()
	{
		//テーブル名からは取得できないのでinfo取得
		if(empty($this->_table_info)){
			$this->_table_info = $this->tableInfo();
		}
		
		return new DynamoDBTagResourceBuilder($this->_dynamodb, $this->_table_info["TableArn"]);
	}
	
	/**
	 * tagを削除
	 * @return DynamoDBUnTagResourceBuilder
	 */
	public function deleteTag()
	{
		//テーブル名からは取得できないのでinfo取得
		if(empty($this->_table_info)){
			$this->_table_info = $this->tableInfo();
		}
		
		return new DynamoDBUnTagResourceBuilder($this->_dynamodb, $this->_table_info["TableArn"]);
	}
	
	
	
	//-----------------------------------------------------------
	// アイテム系
	//-----------------------------------------------------------
	
	/**
	 * クエリ作成開始
	 * @return DynamoDBQueryBuilder
	 */
	public function query()
	{
		return new DynamoDBQueryBuilder($this->_dynamodb, $this->_table_name);
	}

	/**
	 * クエリ作成開始
	 * @return DynamoDBScanBuilder
	 */
	public function scan()
	{
		return new DynamoDBScanBuilder($this->_dynamodb, $this->_table_name);
	}
	
	/**
	 * 取得する
	 * @return DynamoDBGetItemBuilder
	 */
	public function getItem(){
		return new DynamoDBGetItemBuilder($this->_dynamodb, $this->_table_name);
	}
	
	/**
	 * 一括取得する
	 * @return DynamoDBGetItemMultiForTableBuilder
	 */
	public function getMultiItem(){
		return new DynamoDBGetItemMultiForTableBuilder($this->_dynamodb, $this->_table_name);
	}

	/**
	 * 取得する
	 * @return DynamoDBPutItemBuilder
	 */
	public function putItem(){
		return new DynamoDBPutItemBuilder($this->_dynamodb, $this->_table_name);
	}
	
	/**
	 * 更新クエリ
	 * @return DynamoDBUpdateItemBuilder
	 */
	public function updateItem(){
		return new DynamoDBUpdateItemBuilder($this->_dynamodb, $this->_table_name);
	}
	
	/**
	 * 削除クエリ
	 * @return DynamoDBDeleteItemBuilder
	 */
	public function deleteItem(){
		return new DynamoDBDeleteItemBuilder($this->_dynamodb, $this->_table_name);
	}
	
	/**
	 * 一括更新クエリ
	 * @return DynamoDBWriteItemMultiForTableBuilder
	 */
	public function writeMultiItem(){
		return new DynamoDBWriteItemMultiForTableBuilder($this->_dynamodb, $this->_table_name);
	}
	
}
