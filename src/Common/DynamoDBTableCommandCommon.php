<?php
namespace konnosena\DynamoDB\Common;

use konnosena\DynamoDB\DynamoDBBuilder;

/**
 * DynamoDBのテーブルに対してのコマンド基底クラス
 * @property string $_table_name
 */
abstract class DynamoDBTableCommandCommon extends DynamoDBGlobalCommandCommon
{
	/**
	 * テーブル名
	 * @var string
	 */
	protected $_table_name = "";
	
	/**
	 * constructor.
	 * @param DynamoDBBuilder $dynamodb
	 * @param $table_name
	 */
	public function __construct(DynamoDBBuilder $dynamodb, $table_name)
	{
		parent::__construct($dynamodb);
		$this->_table_name = $table_name;
	}
	
	
	/**
	 * テーブル名の取得
	 * @return string
	 */
	public function getTableName(): string
	{
		return $this->_table_name;
	}
	
}
