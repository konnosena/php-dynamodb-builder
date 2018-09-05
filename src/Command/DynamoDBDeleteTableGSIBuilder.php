<?php
namespace konnosena\DynamoDB\Command;

use konnosena\DynamoDB\Common\DynamoDBUpdateTableCommon;
use konnosena\DynamoDB\Index\DynamoDBGSI;
use konnosena\DynamoDB\Index\DynamoDBLSI;


/**
 * DynamoDBUpdateTableDeleteGSIBuilder
 * テーブルのGSI削除
 * @property DynamoDBLSI[] $_local_secondary_indexes
 * @property DynamoDBGSI[] $_global_secondary_indexes
 */
class DynamoDBDeleteTableGSIBuilder extends DynamoDBUpdateTableCommon
{
	const ID = "L804";
	const COMMAND_NAME = "DeleteTableGSI";
	
	//インデックス
	protected $_delete_gsi_names = [];
	
	//----------------------------------------------------------------------------------
	// クエリ発行前
	//----------------------------------------------------------------------------------
	
	/**
	 * GlobalSecondaryIndexの削除
	 * @param string $gsi_index_name
	 * @return $this
	 */
	public function deleteGSI($gsi_index_name)
	{
		$this->_delete_gsi_names[] = $gsi_index_name;
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
		foreach ($this->_delete_gsi_names as $gsi) {
			$gsi_updates[] = [
				"Delete" => $gsi->getRequestParams()
			];
		}
		
		return [
			"TableName" => $this->_table_name,
			"GlobalSecondaryIndexUpdates" => $gsi_updates
		];
	}
	
}

