<?php
namespace konnosena\DynamoDB\RequestParams;

use Aws\DynamoDb\SetValue;

/**
 * Trait DynamoDBUpdateExpressionTrait
 */
trait DynamoDBUpdateExpressionTrait{
	
	//更新内容
	protected $_update_sets = [];
	protected $_update_removes = [];
	protected $_update_adds = [];
	protected $_update_deletes = [];
	
	/**
	 * Setを追加
	 * @param string $key
	 * @param mixed $value
	 * @return $this
	 */
	public function addUpdateParam($key, $value){

		//プレイスホルダに変換
		$key = $this->getAttributeNamePlaceHolder($key);
		$value = $this->getAttributeValuePlaceHolder($value);
		$this->_update_sets[] = $key ." = ". $value;
		
		return $this;
	}
	
	/**
	 * Setを追加
	 * @param array $key_value_list
	 * @return $this
	 */
	public function addUpdateParams($key_value_list){

		foreach ($key_value_list as $key => $value){
			$this->addUpdateParam($key, $value);
		}
		
		return $this;
	}
	
	

	/**
	 * Setを加算
	 * @param string $key
	 * @param int $num
	 * @return $this
	 */
	public function addUpdateIncrement($key, $num){
		
		//プレイスホルダに変換
		$key = $this->getAttributeNamePlaceHolder($key);
		$num = $this->getAttributeValuePlaceHolder($num);
		
		$this->_update_sets[] = "$key  = $key + $num";
		return $this;
	}

	/**
	 * Setを加算
	 * @param array $key_value_list
	 * @return $this
	 */
	public function addUpdateIncrements($key_value_list){
		
		foreach ($key_value_list as $key => $value) {
			$this->addUpdateIncrement($key, $value);
		}
		return $this;
	}

	
	
	/**
	 * Setを減算
	 * @param string $key
	 * @param int $num
	 * @return $this
	 */
	public function addUpdateDecrement($key, $num){
		
		//プレイスホルダに変換
		$key = $this->getAttributeNamePlaceHolder($key);
		$num = $this->getAttributeValuePlaceHolder($num);
		
		$this->_update_sets[] = "$key  = $key - $num";
		return $this;
	}
	
	/**
	 * Setを減算
	 * @param $key_value_list
	 * @return $this
	 */
	public function addUpdateDecrements($key_value_list){
		
		foreach ($key_value_list as $key => $value) {
			$this->addUpdateDecrement($key, $value);
		}
		return $this;
	}

	
	
	/**
	 * リストに追加
	 * @param string $key
	 * @param array|mixed $list
	 * @return $this
	 */
	public function addUpdateAppendList($key, $list){
		
		//配列じゃない場合、配列に格納する
		if(!is_array($list)){
			$list = [$list];
		}
		
		//プレイスホルダに変換
		$key = $this->getAttributeNamePlaceHolder($key);
		$list = $this->getAttributeValuePlaceHolder($list);
		
		$this->_update_sets[] = "$key = list_append($key, $list)";
		
		return $this;
	}
	
	/**
	 * リストに追加
	 * @param $key_value_list
	 * @return $this
	 */
	public function addUpdateAppendLists($key_value_list){
		
		foreach ($key_value_list as $key => $value) {
			$this->addUpdateAppendList($key, $value);
		}
		
		return $this;
	}

	/**
	 * 要素がない場合だけ入れる
	 * @param string $key
	 * @param mixed $value
	 * @return $this
	 */
	public function addUpdateIfNotExist($key, $value){
		
		//プレイスホルダに変換
		$key = $this->getAttributeNamePlaceHolder($key);
		$value = $this->getAttributeValuePlaceHolder($value);
		
		$this->_update_sets[] = "$key = if_not_exists($key, $value)";
		return $this;
	}

	/**
	 * 要素がない場合だけ入れる
	 * @param mixed $key_value_list
	 * @return $this
	 */
	public function addUpdateIfNotExists($key_value_list){
		
		foreach ($key_value_list as $key => $value) {
			$this->addUpdateIfNotExist($key, $value);
		}
		
		return $this;
	}
	
	/**
	 * パラメータの削除
	 * @param string $key
	 * @return $this
	 */
	public function addUpdateRemoveParam($key){
		
		//プレイスホルダに変換
		$key = $this->getAttributeNamePlaceHolder($key);
		
		$this->_update_removes[] = $key;
		return $this;
	}
	
	/**
	 * パラメータの削除
	 * @param string[] $keys
	 * @return $this
	 */
	public function addUpdateRemoveParams($keys){
		
		foreach ($keys as $key) {
			$this->addUpdateRemoveParam($key);
		}

		return $this;
	}

	
	
	/**
	 * SetListを置き換え
	 * @param string $key
	 * @param mixed $set_list
	 * @return $this
	 */
	public function addUpdateReplaceSetList($key, $set_list){

		//配列じゃない場合、配列に格納する
		if(!is_array($set_list)){
			$set_list = [$set_list];
		}
		
		//プレイスホルダに変換
		$key = $this->getAttributeNamePlaceHolder($key);
		$value = $this->getAttributeValuePlaceHolder(new SetValue($set_list));
		$this->_update_sets[] = $key ." = ". $value;
		
		return $this;
	}

	/**
	 * SetListに追加
	 * @param string $key
	 * @param mixed $set_list
	 * @return $this
	 */
	public function addUpdateAppendSetList($key, $set_list){
		
		//配列じゃない場合、配列に格納する
		if(!is_array($set_list)){
			$set_list = [$set_list];
		}

		//プレイスホルダに変換
		$key = $this->getAttributeNamePlaceHolder($key);
		$value = $this->getAttributeValuePlaceHolder(new SetValue($set_list));
		$this->_update_adds[] = $key ." ". $value;
		
		return $this;
	}
	
	/**
	 * セットリストの要素を削除する
	 * @param string $key
	 * @param mixed $set_list
	 * @return $this
	 */
	public function addUpdateDeleteSetList($key, $set_list){
		
		if(!is_array($set_list)){
			$set_list = [$set_list];
		}
		
		//セット型である必要があるので、すべて同じ型じゃないとだめ？
		
		//プレイスホルダに変換
		$key = $this->getAttributeNamePlaceHolder($key);
		$set_list = $this->getAttributeValuePlaceHolder(new SetValue($set_list));
		
		$this->_update_deletes[] = "$key $set_list";
		return $this;
	}


	/**
	 * セットリストの要素を削除する
	 * @param array $set_list
	 * @return $this
	 */
	public function addUpdateDeleteSetLists($set_list){
		
		foreach ($set_list as $key => $list) {
			$this->addUpdateDeleteSetList($key, $list);
		}
		
		return $this;
	}

	
	/**
	 * Expressionの文字列を取得
	 * @return string
	 */
	protected function getUpdateExpressionString(){
		
		$expression = "";
		if(!empty($this->_update_sets)){
			$expression .= "SET ".implode(",", $this->_update_sets)." ";
		}
		if(!empty($this->_update_adds)){
			$expression .= "ADD ".implode(",", $this->_update_adds)." ";
		}
		if(!empty($this->_update_removes)){
			$expression .= "REMOVE ".implode(",", $this->_update_removes)." ";
		}
		if(!empty($this->_update_deletes)){
			$expression .= "DELETE ".implode(",", $this->_update_deletes)." ";
		}
		
		return $expression;
	}
	
}