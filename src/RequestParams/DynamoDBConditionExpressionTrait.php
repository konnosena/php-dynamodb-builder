<?php
namespace konnosena\DynamoDB\RequestParams;

/**
 * Trait DynamoDBUpdateExpressionTrait
 */
trait DynamoDBConditionExpressionTrait
{
	//更新内容
	protected $_conditions = [];
	
	
	/**
	 * 検索条件 =
	 * @param string $key
	 * @param $value
	 * @param string $logical_operator
	 * @return $this
	 */
	public function addConditionEqual($key, $value, $logical_operator = "AND")
	{
		return $this->addCondition($key, "=", $value, $logical_operator);
	}
	
	/**
	 * 検索条件 >
	 * @param string $key
	 * @param $value
	 * @param string $logical_operator
	 * @return $this
	 */
	public function addConditionMore($key, $value, $logical_operator = "AND")
	{
		return $this->addCondition($key, ">", $value, $logical_operator);
	}
	
	
	/**
	 * 検索条件 >=
	 * @param string $key
	 * @param $value
	 * @param string $logical_operator
	 * @return $this
	 */
	public function addConditionMoreAnd($key, $value, $logical_operator = "AND")
	{
		return $this->addCondition($key, ">=", $value, $logical_operator);
	}
	
	
	/**
	 * 検索条件 <
	 * @param string $key
	 * @param $value
	 * @param string $logical_operator
	 * @return $this
	 */
	public function addConditionLess($key, $value, $logical_operator = "AND")
	{
		return $this->addCondition($key, "<", $value, $logical_operator);
	}
	
	
	/**
	 * 検索条件 <=
	 * @param string $key
	 * @param $value
	 * @param string $logical_operator
	 * @return $this
	 */
	public function addConditionLessAnd($key, $value, $logical_operator = "AND")
	{
		return $this->addCondition($key, "<=", $value, $logical_operator);
	}
	
	
	/**
	 * 検索条件 BETWEEN AND
	 * @param string $key
	 * @param $start_value
	 * @param $end_value
	 * @param string $logical_operator
	 * @return $this
	 */
	public function addConditionBetween($key, $start_value, $end_value, $logical_operator = "AND")
	{
		
		//プレイスホルダに変換
		$key = $this->getAttributeNamePlaceHolder($key);
		$start_value = $this->getAttributeValuePlaceHolder($start_value);
		$end_value = $this->getAttributeValuePlaceHolder($end_value);
		
		//形成する
		if (empty($this->_conditions)) {
			$logical_operator = "";
		}
		$this->_conditions[] = "$logical_operator $key BETWEEN $start_value AND $end_value";
		
		return $this;
	}
	
	
	/**
	 * 検索条件 begins_with()
	 * @param string $key
	 * @param $value
	 * @param string $logical_operator
	 * @return $this
	 */
	public function addConditionBegin($key, $value, $logical_operator = "AND")
	{
		//プレイスホルダに変換
		$key = $this->getAttributeNamePlaceHolder($key);
		$value = $this->getAttributeValuePlaceHolder($value);
		
		//形成する
		if (empty($this->_conditions)) {
			$logical_operator = "";
		}
		$this->_conditions[] = "$logical_operator begins_with($key,$value)";
		
		return $this;
	}
	
	
	/**
	 * 検索条件 attribute_exists()
	 * @param string $key
	 * @param string $logical_operator
	 * @return $this
	 */
	public function addConditionAttributeExist($key, $logical_operator = "AND")
	{
		//プレイスホルダに変換
		$key = $this->getAttributeNamePlaceHolder($key);
		
		//形成する
		if (empty($this->_conditions)) {
			$logical_operator = "";
		}
		$this->_conditions[] = "$logical_operator attribute_exists($key)";
		
		return $this;
	}
	
	/**
	 * 検索条件 attribute_not_exists()
	 * @param string $key
	 * @param string $logical_operator
	 * @return $this
	 */
	public function addConditionAttributeNotExist($key, $logical_operator = "AND")
	{
		//プレイスホルダに変換
		$key = $this->getAttributeNamePlaceHolder($key);
		
		//形成する
		if (empty($this->_conditions)) {
			$logical_operator = "";
		}
		$this->_conditions[] = "$logical_operator attribute_not_exists($key)";
		
		return $this;
	}
	
	/**
	 * 検索条件 attribute_type()
	 * @param string $key
	 * @param string $dybanodb_attribute_type
	 * @param string $logical_operator
	 * @return $this
	 */
	public function addConditionAttributeType($key, $dybanodb_attribute_type, $logical_operator = "AND")
	{
		//プレイスホルダに変換
		$key = $this->getAttributeNamePlaceHolder($key);
		$value = $this->getAttributeValuePlaceHolder($dybanodb_attribute_type);
		
		//形成する
		if (empty($this->_conditions)) {
			$logical_operator = "";
		}
		$this->_conditions[] = "$logical_operator attribute_type($key,$value)";
		
		return $this;
	}
	
	
	/**
	 * 検索条件 contain()
	 * @param string $key
	 * @param $value
	 * @param string $logical_operator
	 * @return $this
	 */
	public function addConditionContain($key, $value, $logical_operator = "AND")
	{
		//プレイスホルダに変換
		$key = $this->getAttributeNamePlaceHolder($key);
		$value = $this->getAttributeValuePlaceHolder($value);
		
		//形成する
		if (empty($this->_conditions)) {
			$logical_operator = "";
		}
		$this->_conditions[] = "$logical_operator contains($key,$value)";
		
		return $this;
	}
	

	/**
	 * 検索条件 =
	 * @param string $key
	 * @param $value
	 * @param string $logical_operator
	 * @return $this
	 */
	public function addConditionSizeEqual($key, $value, $logical_operator = "AND")
	{
		return $this->addSizeCondition($key, "=", $value, $logical_operator);
	}
	
	/**
	 * 検索条件 size() >
	 * @param string $key
	 * @param $value
	 * @param string $logical_operator
	 * @return $this
	 */
	public function addConditionSizeMore($key, $value, $logical_operator = "AND")
	{
		return $this->addSizeCondition($key, ">", $value, $logical_operator);
	}
	
	
	/**
	 * 検索条件 size() >=
	 * @param string $key
	 * @param $value
	 * @param string $logical_operator
	 * @return $this
	 */
	public function addConditionSizeMoreAnd($key, $value, $logical_operator = "AND")
	{
		return $this->addSizeCondition($key, ">=", $value, $logical_operator);
	}
	
	
	/**
	 * 検索条件 size() <
	 * @param string $key
	 * @param $value
	 * @param string $logical_operator
	 * @return $this
	 */
	public function addConditionSizeLess($key, $value, $logical_operator = "AND")
	{
		return $this->addSizeCondition($key, "<", $value, $logical_operator);
	}
	
	
	/**
	 * 検索条件 size() <=
	 * @param string $key
	 * @param $value
	 * @param string $logical_operator
	 * @return $this
	 */
	public function addConditionSizeLessAnd($key, $value, $logical_operator = "AND")
	{
		return $this->addSizeCondition($key, "<=", $value, $logical_operator);
	}

	
	
	
	/**
	 * 更新条件の追加
	 * @param $key
	 * @param $compare
	 * @param $value
	 * @param string $logical_operator
	 * @return $this
	 */
	protected function addCondition($key, $compare, $value, $logical_operator = "AND")
	{
		//プレイスホルダに変換
		$key = $this->getAttributeNamePlaceHolder($key);
		$value = $this->getAttributeValuePlaceHolder($value);
		
		//形成する
		if (empty($this->_conditions)) {
			$logical_operator = "";
		}
		$this->_conditions[] = "$logical_operator $key $compare $value";
		
		return $this;
	}
	

	
	/**
	 * 更新条件の追加
	 * @param $key
	 * @param $compare
	 * @param $value
	 * @param string $logical_operator
	 * @return $this
	 */
	protected function addSizeCondition($key, $compare, $value, $logical_operator = "AND")
	{
		//プレイスホルダに変換
		$key = $this->getAttributeNamePlaceHolder($key);
		$value = $this->getAttributeValuePlaceHolder($value);
		
		//形成する
		if (empty($this->_conditions)) {
			$logical_operator = "";
		}
		$this->_conditions[] = "$logical_operator size($key) $compare $value";
		
		return $this;
	}

	/**
	 * ConditionExpressionを取得する
	 */
	protected function getConditionExpression()
	{
		return implode(" ", $this->_conditions);
	}
	
	
}