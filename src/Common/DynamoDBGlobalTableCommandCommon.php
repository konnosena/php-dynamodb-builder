<?php

namespace konnosena\DynamoDB\Common;

use konnosena\DynamoDB\DynamoDBBuilder;


/**
 * DynamoDBのグローバルテーブルに対してのコマンド基底クラス
 * @property string $_table_name
 */
abstract class DynamoDBGlobalTableCommandCommon extends DynamoDBGlobalCommandCommon
{
	/**
	 * テーブル名
	 * @var string
	 */
	protected $_global_table_name = "";
	
	/**
	 * constructor.
	 * @param DynamoDBBuilder $dynamodb
	 * @param $global_table_name
	 */
	public function __construct(DynamoDBBuilder $dynamodb, $global_table_name)
	{
		parent::__construct($dynamodb);
		$this->_global_table_name = $global_table_name;
	}

}
