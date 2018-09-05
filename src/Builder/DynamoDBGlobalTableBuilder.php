<?php

namespace konnosena\DynamoDB\Builder;

use konnosena\DynamoDB\Command\DynamoDBCreateGlobalTableBuilder;
use konnosena\DynamoDB\Command\DynamoDBDescribeGlobalTableBuilder;
use konnosena\DynamoDB\Command\DynamoDBListGlobalTablesBuilder;
use konnosena\DynamoDB\Command\DynamoDBUpdateGlobalTableBuilder;

/**
 * DynamoDBのグローバルテーブル系ビルダを整理する起点
 */
class DynamoDBGlobalTableBuilder
{
	const ID = "L804";
	
	private $_dynamodb = null;
	private $_global_table_name = "";
	
	/**
	 * コンストラクタ
	 * @param $dynamodb
	 * @param $global_table_name
	 */
	public function __construct($dynamodb, $global_table_name)
	{
		$this->_dynamodb = $dynamodb;
		$this->_global_table_name = $global_table_name;
	}
	
	//-----------------------------------------------------------
	// グローバルテーブルビルダ系
	//-----------------------------------------------------------
	
	/**
	 * グローバルテーブル作成
	 * @return DynamoDBCreateGlobalTableBuilder
	 */
	public function create()
	{
		return new DynamoDBCreateGlobalTableBuilder($this->_dynamodb, $this->_global_table_name);
	}

	/**
	 * グローバルテーブル更新
	 * @return DynamoDBUpdateGlobalTableBuilder
	 */
	public function update()
	{
		return new DynamoDBUpdateGlobalTableBuilder($this->_dynamodb, $this->_global_table_name);
	}

	/**
	 * グローバルテーブル一覧取得
	 * @return DynamoDBListGlobalTablesBuilder
	 */
	public function list()
	{
		return new DynamoDBListGlobalTablesBuilder($this->_dynamodb);
	}
	
	/**
	 * グローバルテーブル情報取得
	 * @return DynamoDBDescribeGlobalTableBuilder
	 */
	public function info()
	{
		return new DynamoDBDescribeGlobalTableBuilder($this->_dynamodb, $this->_global_table_name);
	}
	
	
}
