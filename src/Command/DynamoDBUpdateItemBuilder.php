<?php

namespace konnosena\DynamoDB\Command;

use GuzzleHttp\Promise\Promise;
use konnosena\DynamoDB\Common\DynamoDBFallbackableUpdateItemCommon;
use konnosena\DynamoDB\Exception\DynamoDB_RequestException;
use konnosena\DynamoDB\RequestParams\DynamoDBUpdateExpressionTrait;

/**
 * DynamoDBUpdateItemBuilder
 */
class DynamoDBUpdateItemBuilder extends DynamoDBFallbackableUpdateItemCommon
{
	//使うやつ
	use DynamoDBUpdateExpressionTrait;
	
	const ID = "L804";
	const COMMAND_NAME = "UpdateItem";
	
	//----------------------------------------------------------------------------------
	// クエリ発行前
	//----------------------------------------------------------------------------------
	
	
	//----------------------------------------------------------------------------------
	// クエリ発行
	//----------------------------------------------------------------------------------
	
	/**
	 * リクエストの作成
	 * @return array
	 */
	public function createRequestParams()
	{
		$request_params = [
			"TableName" => $this->_table_name,        //テーブル名
			"Key" => $this->_marshaler->marshalItem($this->getKey()),        //更新されるキー
			"UpdateExpression" => $this->getUpdateExpressionString(),        //更新内容
		];
		
		//更新条件の指定があったら指定
		$condition_expressions = $this->getConditionExpression();
		if (!empty($condition_expressions)) {
			$request_params['ConditionExpression'] = $condition_expressions;
		}
		
		//プレイスホルダを設定する
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
		return $this->_dynamodb->updateItemAsync($request_params);
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
		
		//更新内容があるかどうか
		if (empty($this->_update_deletes) &&
			empty($this->_update_removes) &&
			empty($this->_update_sets) &&
			empty($this->_update_adds)
		) {
			throw new DynamoDB_RequestException("更新内容が指定されていません");
		}
	}
}
