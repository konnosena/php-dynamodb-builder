<?php
namespace konnosena\DynamoDB\RequestParams;


/**
 * Trait DynamoDBFilterExpressionTrait
 */
trait DynamoDBFilterExpressionTrait
{
	//更新内容
	protected $_filters = [];
	
	
	/**
	 * 検索条件 =
	 * @param string $key
	 * @param $value
	 * @param string $logical_operator
	 * @return $this
	 */
	public function addFilterEqual($key, $value, $logical_operator = "AND")
	{
		return $this->addFilter($key, "=", $value, $logical_operator);
	}
	
	/**
	 * 検索条件 >
	 * @param string $key
	 * @param $value
	 * @param string $logical_operator
	 * @return $this
	 */
	public function addFilterMore($key, $value, $logical_operator = "AND")
	{
		return $this->addFilter($key, ">", $value, $logical_operator);
	}
	
	
	/**
	 * 検索条件 >=
	 * @param string $key
	 * @param $value
	 * @param string $logical_operator
	 * @return $this
	 */
	public function addFilterMoreAnd($key, $value, $logical_operator = "AND")
	{
		return $this->addFilter($key, ">=", $value, $logical_operator);
	}
	
	
	/**
	 * 検索条件 <
	 * @param string $key
	 * @param $value
	 * @param string $logical_operator
	 * @return $this
	 */
	public function addFilterLess($key, $value, $logical_operator = "AND")
	{
		return $this->addFilter($key, "<", $value, $logical_operator);
	}
	
	
	/**
	 * 検索条件 <=
	 * @param string $key
	 * @param $value
	 * @param string $logical_operator
	 * @return $this
	 */
	public function addFilterLessAnd($key, $value, $logical_operator = "AND")
	{
		return $this->addFilter($key, "<=", $value, $logical_operator);
	}
	
	
	/**
	 * 検索条件 BETWEEN AND
	 * @param string $key
	 * @param $start_value
	 * @param $end_value
	 * @param string $logical_operator
	 * @return $this
	 */
	public function addFilterBetween($key, $start_value, $end_value, $logical_operator = "AND")
	{
		
		//プレイスホルダに変換
		$key = $this->getAttributeNamePlaceHolder($key);
		$start_value = $this->getAttributeValuePlaceHolder($start_value);
		$end_value = $this->getAttributeValuePlaceHolder($end_value);
		
		//形成する
		if (empty($this->_filters)) {
			$logical_operator = "";
		}
		$this->_filters[] = "$logical_operator $key BETWEEN $start_value AND $end_value";
		
		return $this;
	}
	
	
	/**
	 * 検索条件 begins_with()
	 * @param string $key
	 * @param $value
	 * @param string $logical_operator
	 * @return $this
	 */
	public function addFilterBegin($key, $value, $logical_operator = "AND")
	{
		//プレイスホルダに変換
		$key = $this->getAttributeNamePlaceHolder($key);
		$value = $this->getAttributeValuePlaceHolder($value);
		
		//形成する
		if (empty($this->_filters)) {
			$logical_operator = "";
		}
		$this->_filters[] = "$logical_operator begins_with($key,$value)";
		
		return $this;
	}
	
	
	/**
	 * 検索条件 attribute_exists()
	 * @param string $key
	 * @param string $logical_operator
	 * @return $this
	 */
	public function addFilterAttributeExist($key, $logical_operator = "AND")
	{
		//プレイスホルダに変換
		$key = $this->getAttributeNamePlaceHolder($key);
		
		//形成する
		if (empty($this->_filters)) {
			$logical_operator = "";
		}
		$this->_filters[] = "$logical_operator $key attribute_exists($key)";
		
		return $this;
	}
	
	/**
	 * 検索条件 attribute_not_exists()
	 * @param string $key
	 * @param string $logical_operator
	 * @return $this
	 */
	public function addFilterAttributeNotExist($key, $logical_operator = "AND")
	{
		//プレイスホルダに変換
		$key = $this->getAttributeNamePlaceHolder($key);
		
		//形成する
		if (empty($this->_filters)) {
			$logical_operator = "";
		}
		$this->_filters[] = "$logical_operator $key attribute_not_exists($key)";
		
		return $this;
	}
	
	/**
	 * 検索条件 attribute_type()
	 * @param string $key
	 * @param string $dybanodb_attribute_type
	 * @param string $logical_operator
	 * @return $this
	 */
	public function addFilterAttributeType($key, $dybanodb_attribute_type, $logical_operator = "AND")
	{
		//プレイスホルダに変換
		$key = $this->getAttributeNamePlaceHolder($key);
		$value = $this->getAttributeValuePlaceHolder($dybanodb_attribute_type);
		
		//形成する
		if (empty($this->_filters)) {
			$logical_operator = "";
		}
		$this->_filters[] = "$logical_operator $key attribute_type($key,$value)";
		
		return $this;
	}
	
	
	/**
	 * 検索条件 contain()
	 * @param string $key
	 * @param $value
	 * @param string $logical_operator
	 * @return $this
	 */
	public function addFilterContain($key, $value, $logical_operator = "AND")
	{
		//プレイスホルダに変換
		$key = $this->getAttributeNamePlaceHolder($key);
		$value = $this->getAttributeValuePlaceHolder($value);
		
		//形成する
		if (empty($this->_filters)) {
			$logical_operator = "";
		}
		$this->_filters[] = "$logical_operator $key contains($key,$value)";
		
		return $this;
	}
	

	/**
	 * 検索条件 =
	 * @param string $key
	 * @param $value
	 * @param string $logical_operator
	 * @return $this
	 */
	public function addFilterSizeEqual($key, $value, $logical_operator = "AND")
	{
		return $this->addSizeFilter($key, "=", $value, $logical_operator);
	}
	
	/**
	 * 検索条件 size() >
	 * @param string $key
	 * @param $value
	 * @param string $logical_operator
	 * @return $this
	 */
	public function addFilterSizeMore($key, $value, $logical_operator = "AND")
	{
		return $this->addSizeFilter($key, ">", $value, $logical_operator);
	}
	
	
	/**
	 * 検索条件 size() >=
	 * @param string $key
	 * @param $value
	 * @param string $logical_operator
	 * @return $this
	 */
	public function addFilterSizeMoreAnd($key, $value, $logical_operator = "AND")
	{
		return $this->addSizeFilter($key, ">=", $value, $logical_operator);
	}
	
	
	/**
	 * 検索条件 size() <
	 * @param string $key
	 * @param $value
	 * @param string $logical_operator
	 * @return $this
	 */
	public function addFilterSizeLess($key, $value, $logical_operator = "AND")
	{
		return $this->addSizeFilter($key, "<", $value, $logical_operator);
	}
	
	
	/**
	 * 検索条件 size() <=
	 * @param string $key
	 * @param $value
	 * @param string $logical_operator
	 * @return $this
	 */
	public function addFilterSizeLessAnd($key, $value, $logical_operator = "AND")
	{
		return $this->addSizeFilter($key, "<=", $value, $logical_operator);
	}

	
	
	
	/**
	 * 更新条件の追加
	 * @param $key
	 * @param $compare
	 * @param $value
	 * @param string $logical_operator
	 * @return $this
	 */
	protected function addFilter($key, $compare, $value, $logical_operator = "AND")
	{
		//プレイスホルダに変換
		$key = $this->getAttributeNamePlaceHolder($key);
		$value = $this->getAttributeValuePlaceHolder($value);
		
		//形成する
		if (empty($this->_filters)) {
			$logical_operator = "";
		}
		$this->_filters[] = "$logical_operator $key $compare $value";
		
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
	protected function addSizeFilter($key, $compare, $value, $logical_operator = "AND")
	{
		//プレイスホルダに変換
		$key = $this->getAttributeNamePlaceHolder($key);
		$value = $this->getAttributeValuePlaceHolder($value);
		
		//形成する
		if (empty($this->_filters)) {
			$logical_operator = "";
		}
		$this->_filters[] = "$logical_operator size($key) $compare $value";
		
		return $this;
	}

	/**
	 * FilterExpressionを取得する
	 */
	protected function getFilterExpression()
	{
		return trim(implode(" ", $this->_filters));
	}
	
	
}