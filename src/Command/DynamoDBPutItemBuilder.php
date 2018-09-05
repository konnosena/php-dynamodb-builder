<?php
namespace konnosena\DynamoDB\Command;

use konnosena\DynamoDB\Common\DynamoDBFallbackableUpdateItemCommon;
use konnosena\DynamoDB\Exception\DynamoDB_RequestException;


/**
 * DynamoDBPut
 * キーの内容を完全に置き換える（Updateとは違うよ）
 */
class DynamoDBPutItemBuilder extends DynamoDBFallbackableUpdateItemCommon
{
	const ID = "L804";
	const COMMAND_NAME = "PutItem";
	
	//更新内容
	protected $_items = [];
	protected $_is_override = false;
	
	//----------------------------------------------------------------------------------
	// クエリ発行前
	//----------------------------------------------------------------------------------
	
	/**
	 * 更新内容
	 * @param $key_name
	 * @param $value
	 * @return $this
	 */
	public function addItem($key_name, $value){
		$this->_items[$key_name] = $value;
		return $this;
	}
	
	/**
	 * 更新アイテムを追加
	 * @param array $_items
	 * @return $this
	 */
	public function setItems($_items){
		$this->_items = $_items;
		return $this;
	}
	
	/**
	 * オーバーライドしていいかどうかを追加
	 * @param bool $is_override
	 * @return $this
	 */
	public function setIsOverride($is_override){
		$this->_is_override = $is_override;
		return $this;
	}
	
	/**
	 * オーバーライドしていいかどうか
	 * @return bool
	 */
	public function isOverride(){
		return $this->_is_override;
		
	}
	
	//----------------------------------------------------------------------------------
	// クエリ発行
	//----------------------------------------------------------------------------------
	/**
	 * リクエストの作成
	 * @return array
	 */
	public function createRequestParams(){
	
		//フィルタの検索条件作成
		$condition_expressions = $this->getConditionExpression();
		
		//-------------------
		// リクエストの作成
		//-------------------
		$request_params = [
			"TableName" => $this->_table_name,
			//キーとアイテムはくっつける
			"Item" => $this->_marshaler->marshalItem(array_merge($this->_items, $this->getKey()))
		];
		
		//フィルタの指定があったら指定
		if (!empty($condition_expressions)) {
			$request_params['ConditionExpression'] = $condition_expressions;
		}
		
		//Expressionのプレースホルダのセット
		$this->setRequestParamsExpressionPlaceHolder($request_params);
		
		//オプションをくっつける
		return array_merge($request_params, $this->_options);
	}
	
	/**
	 * リクエスト部分の実装
	 * @param $request_params
	 * @return \GuzzleHttp\Promise\Promise
	 */
	protected function requestMainAsync($request_params)
	{
		return $this->_dynamodb->putItemAsync($request_params);
	}
	
	
	/**
	 * バリデーション
	 * @throws DynamoDB_RequestException
	 */
	public function validation()
	{
		//アイテムがあるか
		if(empty($this->_items)){
			throw new DynamoDB_RequestException("更新内容が指定されていません");
		}
	}

}

