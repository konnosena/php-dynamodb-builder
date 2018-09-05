<?php

namespace konnosena\DynamoDB\Builder;

use konnosena\DynamoDB\Command\DynamoDBDeleteItemBuilder;
use konnosena\DynamoDB\Command\DynamoDBGetItemBuilder;
use konnosena\DynamoDB\Command\DynamoDBGetItemMultiBuilder;
use konnosena\DynamoDB\Command\DynamoDBPutItemBuilder;
use konnosena\DynamoDB\Command\DynamoDBQueryBuilder;
use konnosena\DynamoDB\Command\DynamoDBScanBuilder;
use konnosena\DynamoDB\Command\DynamoDBUpdateItemBuilder;
use konnosena\DynamoDB\Command\DynamoDBWriteItemMultiBuilder;

/**
 * DynamoDBのアイテム系ビルダを整理する起点
 */
class DynamoDBItemBuilder
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
	// クエリビルダ系
	//-----------------------------------------------------------
	
	/**
	 * クエリ作成開始
	 * @param $table
	 * @return DynamoDBQueryBuilder
	 */
	public function query($table)
	{
		return new DynamoDBQueryBuilder($this->_dynamodb, $table);
	}

	/**
	 * クエリ作成開始
	 * @param $table
	 * @return DynamoDBScanBuilder
	 */
	public function scan($table)
	{
		return new DynamoDBScanBuilder($this->_dynamodb, $table);
	}
	
	/**
	 * 取得する
	 * @param string $table
	 * @return DynamoDBGetItemBuilder
	 */
	public function get($table){
		return new DynamoDBGetItemBuilder($this->_dynamodb, $table);
	}
	
	/**
	 * 一括取得する
	 * @return DynamoDBGetItemMultiBuilder
	 */
	public function getMulti(){
		return new DynamoDBGetItemMultiBuilder($this->_dynamodb);
	}

	/**
	 * 取得する
	 * @param string $table
	 * @return DynamoDBPutItemBuilder
	 */
	public function put($table){
		return new DynamoDBPutItemBuilder($this->_dynamodb, $table);
	}
	
	/**
	 * 更新クエリ
	 * @param string $table
	 * @return DynamoDBUpdateItemBuilder
	 */
	public function update($table){
		return new DynamoDBUpdateItemBuilder($this->_dynamodb, $table);
	}
	
	/**
	 * 削除クエリ
	 * @param string $table
	 * @return DynamoDBDeleteItemBuilder
	 */
	public function delete($table){
		return new DynamoDBDeleteItemBuilder($this->_dynamodb, $table);
	}
	
	/**
	 * 一括更新クエリ
	 * @return DynamoDBWriteItemMultiBuilder
	 */
	public function writeMulti(){
		return new DynamoDBWriteItemMultiBuilder($this->_dynamodb);
	}
	
	
}
