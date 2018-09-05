<?php
namespace konnosena\DynamoDB\Command;

use konnosena\DynamoDB\Common\DynamoDBUpdateTableCommon;
use konnosena\DynamoDB\Index\DynamoDBGSI;
use konnosena\DynamoDB\Index\DynamoDBLSI;
use konnosena\DynamoDB\Option\DynamoDBOptionProvisionedThroughputTrait;
use konnosena\DynamoDB\Option\DynamoDBOptionStreamSpecificationTrait;


/**
 * DynamoDBUpdateTableBuilder
 * テーブルのオプション更新
 * @property DynamoDBLSI[] $_local_secondary_indexes
 * @property DynamoDBGSI[] $_global_secondary_indexes
 */
class DynamoDBUpdateTableBuilder extends DynamoDBUpdateTableCommon
{
	//オプション
	use DynamoDBOptionProvisionedThroughputTrait;
	use DynamoDBOptionStreamSpecificationTrait;
	
	const ID = "L804";
	const COMMAND_NAME = "UpdateTable";
	
	
	//----------------------------------------------------------------------------------
	// クエリ発行前
	//----------------------------------------------------------------------------------
	
	
	//----------------------------------------------------------------------------------
	// クエリ発行
	//----------------------------------------------------------------------------------
	/**
	 * リクエストの作成
	 * @return array
	 */
	public function createRequestParams()
	{
		$request_params = $this->_options;
		$request_params["TableName"] = $this->_table_name;
		
		return $request_params;
	}
	
}

