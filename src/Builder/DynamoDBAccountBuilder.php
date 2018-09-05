<?php

namespace konnosena\DynamoDB\Builder;

use konnosena\DynamoDB\Command\DynamoDBListBackupsBuilder;
use konnosena\DynamoDB\Command\DynamoDBListGlobalTablesBuilder;
use konnosena\DynamoDB\Command\DynamoDBListTablesBuilder;
use konnosena\DynamoDB\Command\DynamoDBListTagsOfResourceBuilder;
use konnosena\DynamoDB\Command\DynamoDBTagResourceBuilder;
use konnosena\DynamoDB\Command\DynamoDBDescribeLimitsBuilder;
use konnosena\DynamoDB\Command\DynamoDBUnTagResourceBuilder;

/**
 * DynamoDBのアカウント系ビルダを整理する起点
 */
class DynamoDBAccountBuilder
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
	// アカウント系
	//-----------------------------------------------------------
	
	/**
	 *　現在のプロビジョニング制限を取得
	 * @return DynamoDBDescribeLimitsBuilder
	 */
	public function limitInfo()
	{
		return new DynamoDBDescribeLimitsBuilder($this->_dynamodb);
	}
	
	/**
	 * テーブル一覧を取得
	 * @return DynamoDBListTablesBuilder
	 */
	public function listTables()
	{
		return new DynamoDBListTablesBuilder($this->_dynamodb);
	}

	/**
	 * グローバルテーブル一覧を取得
	 * @return DynamoDBListGlobalTablesBuilder
	 */
	public function listGlobalTables()
	{
		return new DynamoDBListGlobalTablesBuilder($this->_dynamodb);
	}

	/**
	 * グローバルテーブル一覧を取得
	 * @return DynamoDBListBackupsBuilder
	 */
	public function listBackups()
	{
		return new DynamoDBListBackupsBuilder($this->_dynamodb);
	}

	
	/**
	 * tagを取得
	 * @return DynamoDBListTagsOfResourceBuilder
	 */
	public function listTableTag($resource_arn)
	{
		return new DynamoDBListTagsOfResourceBuilder($this->_dynamodb, $resource_arn);
	}
	
	
	/**
	 * tagを更新
	 * @param $resource_arn
	 * @return DynamoDBTagResourceBuilder
	 */
	public function updateTableTag($resource_arn)
	{
		return new DynamoDBTagResourceBuilder($this->_dynamodb, $resource_arn);
	}
	
	/**
	 * tagを更新
	 * @param $resource_arn
	 * @return DynamoDBUnTagResourceBuilder
	 */
	public function deleteTableTag($resource_arn)
	{
		return new DynamoDBUnTagResourceBuilder($this->_dynamodb, $resource_arn);
	}
	
}
