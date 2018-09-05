<?php
namespace konnosena\DynamoDB\Command;

use konnosena\DynamoDB\DynamoDBBuilder;
use konnosena\DynamoDB\Option\DynamoDBOptionConsumedCapacityTrait;
use konnosena\DynamoDB\Option\DynamoDBOptionReturnItemCollectionMetricsTrait;


/**
 * DynamoDBの挿入、削除の一括（テーブル固定）
 * @property DynamoDBGetItemBuilder[] $get_commands
 */
class DynamoDBWriteItemMultiForTableBuilder extends DynamoDBWriteItemMultiBuilder
{
	use DynamoDBOptionConsumedCapacityTrait;
	use DynamoDBOptionReturnItemCollectionMetricsTrait;
	
	const ID = "L804";
	const COMMAND_NAME = "WriteItemMultiForTable";
	
	protected $_table_name = "";
	
	protected $_delete_keys = [];
	protected $_puts_items = [];
	
	/**
	 * DynamoDBWriteItemMultiForTableBuilder constructor.
	 * @param DynamoDBBuilder $dynamodb
	 * @param $table_name
	 */
	public function __construct(DynamoDBBuilder $dynamodb, $table_name) {
		parent::__construct($dynamodb);
		
		$this->_table_name = $table_name;
	}
	
	/**
	 * 削除キーを追加
	 * @param $key
	 * @param string $table
	 * @return $this
	 */
	public function addDeleteKey($key, $table = null)
	{
		parent::addDeleteKey($table ?? $this->_table_name, $key);
		return $this;
	}
	
	/**
	 * 削除キーを追加
	 * @param $keys
	 * @param string $table
	 * @return $this
	 */
	public function addDeleteKeys($keys, $table = null)
	{
		parent::addDeleteKeys($table ?? $this->_table_name, $keys);
		return $this;
	}
	/**
	 * 削除キーを置き換え
	 * @param array $keys
	 * @param string $table
	 * @return $this
	 */
	public function setDeleteKeys($keys, $table = null)
	{
		parent::setDeleteKeys($table ?? $this->_table_name, $keys);
		return $this;
	}

	/**
	 * 投入アイテムを追加
	 * @param $item
	 * @param string $table
	 * @return $this
	 */
	public function addPutItem($item, $table = null)
	{
		parent::addPutItem($table ?? $this->_table_name, $item);
		return $this;
	}
	
	/**
	 * 削除キーを置き換え
	 * @param array $items
	 * @param string $table
	 * @return $this
	 */
	public function setPutItems($items, $table = null)
	{
		parent::setPutItems($table ?? $this->_table_name, $items);
		return $this;
	}
	
	/**
	 * 削除キーを追加
	 * @param array $items
	 * @param string $table
	 * @return $this
	 */
	public function addPutItems($items, $table = null)
	{
		parent::addPutItems($table ?? $this->_table_name, $items);
		return $this;
	}
	
	
	/**
	 * リクエスト成功
	 * @param $response
	 * @return array
	 */
	protected function success($response)
	{
		return [
			"ConsumedCapacity" => $response["ConsumedCapacity"] ?? [],
			"ItemCollectionMetrics" => $response["ItemCollectionMetrics"][$this->_table_name] ?? [],
			"UnprocessedItems" => $response["UnprocessedItems"][$this->_table_name] ?? []
		];
	}
	
}
