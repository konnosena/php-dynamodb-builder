<?php

namespace konnosena\DynamoDB\Command;

use GuzzleHttp\Promise\Promise;
use konnosena\DynamoDB\Common\DynamoDBFallbackableUpdateItemCommon;
use konnosena\DynamoDB\Exception\DynamoDB_RequestException;


/**
 * DynamoDBDeleteBuilder
 * キーの内容を削除
 */
class DynamoDBDeleteItemBuilder extends DynamoDBFallbackableUpdateItemCommon
{
	const ID = "L804";
	const COMMAND_NAME = "DeleteItem";
	
	//ReturnValue
	const RETURN_VALUE_NONE = "NONE";
	const RETURN_VALUE_ALL_OLD = "ALL_OLD";
	
	//----------------------------------------------------------------------------------
	// クエリ発行
	//----------------------------------------------------------------------------------
	
	/**
	 * リクエストの作成
	 * @return array
	 */
	public function createRequestParams()
	{
		
		//フィルタの検索条件作成
		$condition_expressions = $this->getConditionExpression();
		
		//-------------------
		// リクエストの作成
		//-------------------
		$request_params = [
			"TableName" => $this->_table_name,
			"Key" => $this->_marshaler->marshalItem($this->getKey())
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
	 * @return Promise
	 */
	protected function requestMainAsync($request_params)
	{
		//クエリ発行
		return $this->_dynamodb->deleteItemAsync($request_params);
	}
	
	/**
	 * バリデーション
	 * @throws DynamoDB_RequestException
	 */
	public function validation()
	{
		//キーが有るか
		if (empty($this->getKey())) {
			throw new DynamoDB_RequestException("キーが指定されていません");
		}
	}
}
