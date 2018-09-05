<?php
namespace konnosena\DynamoDB\Command;

use konnosena\DynamoDB\DynamoDBBuilder;

/**
 * DynamoDBのマルチゲット（テーブル固定）
 * @property DynamoDBGetItemBuilder[] $get_commands
 */
class DynamoDBGetItemMultiForTableBuilder extends DynamoDBGetItemMultiBuilder
{
	const ID = "L804";
	const COMMAND_NAME = "GetItemMultiForTable";
	
	protected $_table_name = "";
	
	//特殊なのでTraitは使わない
	protected $_keys = [];
	protected $_columns = [];
	protected $_consistent_reads = [];
	
	public function __construct(DynamoDBBuilder $dynamodb, $table_name) {
		parent::__construct($dynamodb);
		$this->_table_name = $table_name;
	}
	
	/**
	 * キーを追加
	 * @param $key
	 * @param $table_name
	 * @return $this
	 */
	public function addKey($key, $table_name = null)
	{
		parent::addKey($table_name ?? $this->_table_name, $key);
		return $this;
	}
	
	/**
	 * キーを追加
	 * @param $keys
	 * @param $table_name
	 * @return $this
	 */
	public function addKeys($keys, $table_name = null)
	{
		parent::addKeys($table_name ?? $this->_table_name, $keys);
		return $this;
	}
	
	/**
	 * キーを追加
	 * @param array $keys
	 * @param $table_name
	 * @return DynamoDBGetItemMultiBuilder
	 */
	public function setKeys($keys, $table_name = null)
	{
		parent::setKeys($table_name ?? $this->_table_name, $keys);
		return $this;
	}
	
	/**
	 * 取得カラムの設定
	 * @param array $column_names
	 * @param $table_name
	 * @return $this
	 */
	public function addColumn($column_names, $table_name = null)
	{
		parent::addColumn($table_name ?? $this->_table_name, $column_names);
		return $this;
	}
	
	/**
	 * 強い整合性の読み込み
	 * @param bool $bool
	 * @param $table_name
	 * @return $this
	 */
	public function isConsistentRead($bool, $table_name = null)
	{
		parent::isConsistentRead($table_name ?? $this->_table_name, $bool);
		return $this;
	}
	
	/**
	 * リクエスト成功
	 * @param $responses
	 * @return array
	 */
	protected function success($responses)
	{
		//パースしていく
		$results = [];
		foreach ($responses as $response){
			foreach ($response["Responses"] as $table => $rows){
				foreach ($rows as $row){
					$results[] = $this->_marshaler->unmarshalItem($row);
				}
			}
		}
		return $results;
	}
}
