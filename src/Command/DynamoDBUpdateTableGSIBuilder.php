<?php
namespace konnosena\DynamoDB\Command;

use konnosena\DynamoDB\Common\DynamoDBUpdateTableCommon;

/**
 * DynamoDBUpdateTableUpdateGSIBuilder
 * テーブルのGSI更新
 */
class DynamoDBUpdateTableGSIBuilder extends DynamoDBUpdateTableCommon
{
	const ID = "L804";
	const COMMAND_NAME = "UpdateTableGSI";
	
	//インデックス
	protected $_gsi_provisiond_capacities = [];
	
	
	//----------------------------------------------------------------------------------
	// クエリ発行前
	//----------------------------------------------------------------------------------
	
	/**
	 * GlobalSecondaryIndexの削除
	 * @param string $index_name
	 * @param $read_capacity_unit
	 * @param $write_capacity_unit
	 * @return $this
	 */
	public function updateGSIProvisiondThroughput($index_name, $read_capacity_unit, $write_capacity_unit)
	{
		$this->_gsi_provisiond_capacities[$index_name] = [$read_capacity_unit, $write_capacity_unit];
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
		
		foreach ($this->_gsi_provisiond_capacities as $index_name => $capacity) {
			$gsi_updates[] = [
				"Update" => [
					"IndexName" => $index_name,
					'ProvisionedThroughput' => [
						'ReadCapacityUnits' => $capacity[0],
						'WriteCapacityUnits' => $capacity[1]
					],
				]
			];
		}
		
		return [
			"TableName" => $this->_table_name,
			"GlobalSecondaryIndexUpdates" => $gsi_updates
		];
	}
	
}

