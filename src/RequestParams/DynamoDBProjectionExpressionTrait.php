<?php
namespace konnosena\DynamoDB\RequestParams;


/**
 * Trait DynamoDBUpdateExpressionTrait
 */
trait DynamoDBProjectionExpressionTrait
{
	//カラム
	protected $_columns = [];
	
	/**
	 * 取得カラムの設定
	 * @param string $column_name
	 * @return $this
	 */
	public function addColumn($column_name){
		
		//プレイスホルダに変換
		$column_name_ph = $this->getAttributeNamePlaceHolder($column_name);
		
		$this->_columns[] = $column_name_ph;
		
		return $this;
	}
	
	/**
	 * 取得カラムの設定
	 * @param string[] $column_names
	 * @return $this
	 */
	public function addColumns($column_names){
		
		$column_names_ph = [];
		foreach ($column_names as $column_name){
			$column_names_ph[] = $this->getAttributeNamePlaceHolder($column_name);
		}
		
		$this->_columns = array_merge($this->_columns, $column_names_ph);
		
		return $this;
	}

	/**
	 * カラムの取得
	 * @return array
	 */
	public function getColumns(): array
	{
		return $this->_columns;
	}
	
	/**
	 * ProjectionExpressionの取得
	 * @return string
	 */
	protected function getProjectionExpression(){
		return implode(",", $this->_columns);
	}

}