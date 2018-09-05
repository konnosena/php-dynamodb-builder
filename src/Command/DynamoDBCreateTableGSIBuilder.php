<?php

namespace konnosena\DynamoDB\Command;

use konnosena\DynamoDB\Common\DynamoDBUpdateTableCommon;
use konnosena\DynamoDB\Index\DynamoDBGSI;
use konnosena\DynamoDB\RequestParams\DynamoDBAttributeDefinitionsTrait;


/**
 * DynamoDBUpdateTableCreateGSIBuilder
 * テーブルのGSI作成
 */
class DynamoDBCreateTableGSIBuilder extends DynamoDBUpdateTableCommon
{
	//リクエストパラメータ
	use DynamoDBAttributeDefinitionsTrait;
	
	const ID = "L804";
	const COMMAND_NAME = "CreateTableGSI";
	
	//インデックス
	protected $_global_secondary_indexes = [];
	
	//----------------------------------------------------------------------------------
	// クエリ発行前
	//----------------------------------------------------------------------------------
	/**
	 * GlobalSecondaryIndexesの追加
	 * @param DynamoDBGSI $global_secondary_index
	 * @return $this
	 */
	public function addCreateGSI(DynamoDBGSI $global_secondary_index)
	{
		$this->_global_secondary_indexes[] = $global_secondary_index;
		return $this;
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
		//GSIの設定
		$gsi_updates = [];
		foreach ($this->_global_secondary_indexes as $gsi) {
			$gsi_updates[] = [
				"Create" => $gsi->getRequestParams()
			];
		}
		
		return [
			"TableName" => $this->_table_name,
			"AttributeDefinitions" => $this->getAttributeDefinitions(),
			"GlobalSecondaryIndexUpdates" => $gsi_updates
		];
	}
}

