<?php
namespace konnosena\DynamoDB\RequestParams;

trait DynamoDBExpressionCommonTrait{
	
	protected $value_num = 0;
	protected $expression_attribute_names = [];
	protected $expression_attribute_values = [];
	
	
	/**
	 * 名前のプレイスホルダ
	 * @param $name
	 * @return mixed
	 */
	public function getAttributeNamePlaceHolder($name){
		
		//Mapのときは.で区切る
		$list = explode(".", $name);
		
		$key_phs = [];
		foreach ($list as $val){
			$key_ph = "#".$val;
			
			//配列指定があったら除去する
			$val = preg_replace("/\[.+?\]/", "", $val);
			$key_ph = preg_replace("/\[.+?\]/", "", $key_ph);
			
			//キー追加
			if(!isset($this->expression_attribute_names[$key_ph])){
				$this->expression_attribute_names[$key_ph] = $val;
			}
			
			$key_phs[] = "#".$val;
		}
		
		return implode(".", $key_phs);
	}
	
	/**
	 * 値のプレイスホルダ
	 * @param $value
	 * @return mixed
	 */
	public function getAttributeValuePlaceHolder($value){
		
		$val_ph = ":v".$this->value_num++;

		//値追加
		$this->expression_attribute_values[$val_ph] = $value;
		
		return $val_ph;
	}
	
	/**
	 * ExpressionのPlaceHolderを設定する
	 * @param $request_params
	 * @return mixed
	 */
	protected function setRequestParamsExpressionPlaceHolder(&$request_params){
		
		//キー、属性名のプレースホルダの定義
		if (!empty($this->expression_attribute_names)) {
			$request_params['ExpressionAttributeNames'] = $this->expression_attribute_names;
		}
		
		//キー、検索値のプレースホルダの定義
		if (!empty($this->expression_attribute_values)) {
			$request_params['ExpressionAttributeValues'] = $this->_marshaler->marshalItem($this->expression_attribute_values);
		}
		
		return $request_params;
	}

}