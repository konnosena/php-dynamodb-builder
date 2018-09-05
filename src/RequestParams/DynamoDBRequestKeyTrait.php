<?php
namespace konnosena\DynamoDB\RequestParams;

trait DynamoDBRequestKeyTrait{
	
	//キー
	protected $_partition_key = [];
	protected $_sort_key = [];
	
	/**
	 * パーティションキーをセット
	 * @param $key_name
	 * @param $value
	 * @return $this
	 */
	public function setPartitionKey($key_name, $value){
		$this->_partition_key = [$key_name, $value];
		return $this;
	}
	
	/**
	 * ソートキーをセット
	 * @param $key_name
	 * @param $value
	 * @return $this
	 */
	public function setSortKey($key_name, $value){
		$this->_sort_key = [$key_name, $value];
		return $this;
	}
	
	/**
	 * パーティションキーを取得
	 * @return string
	 */
	public function getPartitionKeyName(){
		return $this->_partition_key[0] ?? null;
	}

	/**
	 * パーティションキーを取得
	 * @return string
	 */
	public function getPartitionKeyValue(){
		return $this->_partition_key[1] ?? null;
	}

	/**
	 * ソートキーを取得
	 * @return string
	 */
	public function getSortKeyName(){
		return $this->_sort_key[0] ?? null;
	}

	/**
	 * ソートキーを取得
	 * @return string
	 */
	public function getSortKeyValue(){
		return $this->_sort_key[1] ?? null;
	}
	
	
	/**
	 * キーを設定(1つ目がパーティションキー、2つ目がソートキー)
	 * @param array $keys
	 * @return $this
	 */
	public function setKey($keys){
		
		$this->_partition_key = [];
		$this->_sort_key = [];
		
		foreach ($keys as $key => $val){
			if(empty($this->_partition_key)){
				$this->_partition_key = [$key, $val];
			}
			else{
				$this->_sort_key = [$key, $val];
				break;
			}
		}
		
		return $this;
	}
	
	/**
	 * キーを取得
	 * @return array
	 */
	public function getKey(){
		
		$key = [];
		if(!empty($this->_partition_key)){
			$key[$this->_partition_key[0]] = $this->_partition_key[1];
		}
		if(!empty($this->_sort_key)){
			$key[$this->_sort_key[0]] = $this->_sort_key[1];
		}
		
		return $key;
	}
	
}