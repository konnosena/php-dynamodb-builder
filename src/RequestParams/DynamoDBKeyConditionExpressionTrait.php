<?php
namespace konnosena\DynamoDB\RequestParams;


/**
 * Trait DynamoDBKeyConditionExpressionTrait
 */
trait DynamoDBKeyConditionExpressionTrait
{
	//更新内容
	protected $_partition_key_condition = "";
	protected $_sort_key_condition = "";
	
	
	/**
	 * パーティションキーの指定(１つ目がパーティション、２つめがソートキー)
	 * @param $keys
	 * @return $this
	 */
	public function setKey($keys)
	{
		$pos = 0;
		foreach ($keys as $key => $value){
			
			$key = $this->getAttributeNamePlaceHolder($key);
			$value = $this->getAttributeValuePlaceHolder($value);
			
			if($pos === 0){
				$this->_partition_key_condition = "$key = $value";
			}
			else if($pos === 1){
				$this->_sort_key_condition = "$key = $value";
			}
			
			//次へ
			$pos++;
		}
		
		return $this;
	}

	
	/**
	 * パーティションキーの指定
	 * @param string $key
	 * @param $value
	 * @return $this
	 */
	public function partitionKey($key, $value)
	{
		//プレイスホルダに変換
		$key = $this->getAttributeNamePlaceHolder($key);
		$value = $this->getAttributeValuePlaceHolder($value);
		
		$this->_partition_key_condition = "$key = $value";
		
		return $this;
	}
	
	
	/**
	 * ソートキーの検索条件の指定
	 * @param $key
	 * @param $compare
	 * @param $value
	 * @return $this
	 */
	protected function sortKey($key, $compare, $value)
	{
		//プレイスホルダに変換
		$key = $this->getAttributeNamePlaceHolder($key);
		$value = $this->getAttributeValuePlaceHolder($value);
		
		$this->_sort_key_condition = "$key $compare $value";
		
		return $this;
	}
	
	
	/**
	 * 検索条件 =
	 * @param string $key
	 * @param $value
	 * @return $this
	 */
	public function sortKeyEqual($key, $value)
	{
		return $this->sortKey($key, "=", $value);
	}
	
	/**
	 * 検索条件 >
	 * @param string $key
	 * @param $value
	 * @return $this
	 */
	public function sortKeyMore($key, $value)
	{
		return $this->sortKey($key, ">", $value);
	}
	
	
	/**
	 * 検索条件 >=
	 * @param string $key
	 * @param $value
	 * @return $this
	 */
	public function sortKeyMoreAnd($key, $value)
	{
		return $this->sortKey($key, ">=", $value);
	}
	
	
	/**
	 * 検索条件 <
	 * @param string $key
	 * @param $value
	 * @return $this
	 */
	public function sortKeyLess($key, $value)
	{
		return $this->sortKey($key, "<", $value);
	}
	
	
	/**
	 * 検索条件 <=
	 * @param string $key
	 * @param $value
	 * @return $this
	 */
	public function sortKeyLessAnd($key, $value)
	{
		return $this->sortKey($key, "<=", $value);
	}
	
	
	/**
	 * 検索条件 BETWEEN AND
	 * @param string $key
	 * @param $start_value
	 * @param $end_value
	 * @return $this
	 */
	public function sortKeyBetween($key, $start_value, $end_value)
	{
		
		//プレイスホルダに変換
		$key = $this->getAttributeNamePlaceHolder($key);
		$start_value = $this->getAttributeValuePlaceHolder($start_value);
		$end_value = $this->getAttributeValuePlaceHolder($end_value);
		
		$this->_sort_key_condition = "$key BETWEEN $start_value AND $end_value";
		
		return $this;
	}
	
	
	/**
	 * 検索条件 begins_with()
	 * @param string $key
	 * @param $value
	 * @return $this
	 */
	public function sortKeyBegin($key, $value)
	{
		//プレイスホルダに変換
		$key = $this->getAttributeNamePlaceHolder($key);
		$value = $this->getAttributeValuePlaceHolder($value);
		
		$this->_sort_key_condition = "$key begins_with($key,$value)";
		
		return $this;
	}
	
	
	/**
	 * 検索条件 attribute_exists()
	 * @param string $key
	 * @return $this
	 */
	public function sortKeyAttributeExist($key)
	{
		//プレイスホルダに変換
		$key = $this->getAttributeNamePlaceHolder($key);
		
		$this->_sort_key_condition = "$key attribute_exists($key)";
		
		return $this;
	}
	
	/**
	 * 検索条件 attribute_not_exists()
	 * @param string $key
	 * @return $this
	 */
	public function sortKeyAttributeNotExist($key)
	{
		//プレイスホルダに変換
		$key = $this->getAttributeNamePlaceHolder($key);
		
		$this->_sort_key_condition = "$key attribute_not_exists($key)";
		
		return $this;
	}
	
	/**
	 * 検索条件 attribute_type()
	 * @param string $key
	 * @param string $dybanodb_attribute_type
	 * @return $this
	 */
	public function sortKeyAttributeType($key, $dybanodb_attribute_type)
	{
		//プレイスホルダに変換
		$key = $this->getAttributeNamePlaceHolder($key);
		$value = $this->getAttributeValuePlaceHolder($dybanodb_attribute_type);
		
		$this->_sort_key_condition = "$key attribute_type($key,$value)";
		
		return $this;
	}
	
	
	/**
	 * 検索条件 contain()
	 * @param string $key
	 * @param $value
	 * @return $this
	 */
	public function sortKeyContain($key, $value)
	{
		//プレイスホルダに変換
		$key = $this->getAttributeNamePlaceHolder($key);
		$value = $this->getAttributeValuePlaceHolder($value);
		
		$this->_sort_key_condition = "$key contains($key,$value)";
		
		return $this;
	}
	
	
	/**
	 * 検索条件 =
	 * @param string $key
	 * @param $value
	 * @return $this
	 */
	public function sortKeySizeEqual($key, $value)
	{
		return $this->sizeKeyCondition($key, "=", $value);
	}
	
	/**
	 * 検索条件 size() >
	 * @param string $key
	 * @param $value
	 * @return $this
	 */
	public function sortKeySizeMore($key, $value)
	{
		return $this->sizeKeyCondition($key, ">", $value);
	}
	
	
	/**
	 * 検索条件 size() >=
	 * @param string $key
	 * @param $value
	 * @return $this
	 */
	public function sortKeySizeMoreAnd($key, $value)
	{
		return $this->sizeKeyCondition($key, ">=", $value);
	}
	
	
	/**
	 * 検索条件 size() <
	 * @param string $key
	 * @param $value
	 * @return $this
	 */
	public function sortKeySizeLess($key, $value)
	{
		return $this->sizeKeyCondition($key, "<", $value);
	}
	
	
	/**
	 * 検索条件 size() <=
	 * @param string $key
	 * @param $value
	 * @return $this
	 */
	public function sortKeySizeLessAnd($key, $value)
	{
		return $this->sizeKeyCondition($key, "<=", $value);
	}
	
	/**
	 * 更新条件の追加
	 * @param $key
	 * @param $compare
	 * @param $value
	 * @return $this
	 */
	protected function sizeKeyCondition($key, $compare, $value)
	{
		//プレイスホルダに変換
		$key = $this->getAttributeNamePlaceHolder($key);
		$value = $this->getAttributeValuePlaceHolder($value);
		
		$this->_sort_key_condition = "size($key) $compare $value";
		
		return $this;
	}
	
	/**
	 * KeyConditionExpressionを取得する
	 * @return string
	 */
	protected function getKeyConditionExpressionString()
	{
		$expression = $this->_partition_key_condition;
		
		//ソートキーの指定があれば
		if(!empty($this->_sort_key_condition)){
			$expression .= " AND " .$this->_sort_key_condition;
		}
		
		return $expression;
	}
	
	
}