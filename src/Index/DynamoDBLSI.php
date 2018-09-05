<?php
namespace konnosena\DynamoDB\Index;

use konnosena\DynamoDB\Master\DynamoDBProjectionType;


/**
 * Created by PhpStorm.
 * User: K_Yamada
 * Date: 2018/04/02
 * Time: 18:26
 */

class DynamoDBLSI
{
	//インデックス名
	protected $_index_name = "";
	
	//キー
	protected $_partition_key = null;
	protected $_sort_key = null;
	
	protected $_projection_type = [];
	
	/**
	 * LocalSecondaryIndexes constructor.
	 * @param string $_index_name
	 */
	public function __construct(string $_index_name) {
		$this->_index_name = $_index_name;
		
		//デフォルト
		$this->_projection_type = [
			"ProjectionType" => DynamoDBProjectionType::TYPE_ALL
		];
	}
	
	
	/**
	 * キーの設定
	 * @param $partition_key
	 * @param $sort_key
	 * @return $this
	 */
	public function setKey($partition_key, $sort_key = "")
	{
		$this->_partition_key = $partition_key;
		$this->_sort_key = $sort_key;
		
		return $this;
	}

	
	/**
	 * 取得タイプを設定する
	 * @param string $projection_type 取得タイプ
	 * @param string[] $non_key_attributes キーじゃないけど見れるカラム名配列(タイプがINDEXの時に有効)
	 * @return $this
	 */
	public function setProjectionType($projection_type, $non_key_attributes = [])
	{
		if ($projection_type == DynamoDBProjectionType::TYPE_INCLUDE) {
			
			$this->_projection_type = [
				"ProjectionType" => $projection_type,
				"NonKeyAttributes" => $non_key_attributes
			];
			
		}
		else {
			$this->_projection_type = [
				"ProjectionType" => $projection_type
			];
		}
		return $this;
	}
	
	
	/**
	 * 送信パラメータを取得する
	 * @return array
	 */
	public function getRequestParams()
	{
		//keySchemeを作成
		$key_schemas = [];
		
		//プライマリキー
		$key_schemas[] = [
			"AttributeName" => $this->_partition_key,
			"KeyType" => "HASH"
		];
		
		//ソートキーの指定があったら入れる
		if (!empty($this->_sort_key)) {
			$key_schemas[] = [
				"AttributeName" => $this->_sort_key,
				"KeyType" => "RANGE"
			];
		}
		
		return [
			"IndexName" => $this->_index_name,
			"KeySchema" => $key_schemas,
			"Projection" => $this->_projection_type
		];
	}
	
}