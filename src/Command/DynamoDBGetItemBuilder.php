<?php

namespace konnosena\DynamoDB\Command;

use Aws\DynamoDb\Exception\DynamoDbException;
use GuzzleHttp\Promise\Promise;
use GuzzleHttp\Promise\PromiseInterface;
use konnosena\DynamoDB\Common\DynamoDBTableCommandCommon;
use konnosena\DynamoDB\Exception\DynamoDB_RequestException;
use konnosena\DynamoDB\Option\DynamoDBOptionConsumedCapacityTrait;
use konnosena\DynamoDB\Option\DynamoDBOptionReturnItemCollectionMetricsTrait;
use konnosena\DynamoDB\Option\DynamoDBOptionReturnValueTrait;
use konnosena\DynamoDB\RequestParams\DynamoDBExpressionCommonTrait;
use konnosena\DynamoDB\RequestParams\DynamoDBProjectionExpressionTrait;
use konnosena\DynamoDB\RequestParams\DynamoDBRequestKeyTrait;
use konnosena\DynamoDB\Response\DynamoDBResponseConsumedCapacityTrait;


/**
 * DynamoDBのクエリ組み立て
 */
class DynamoDBGetItemBuilder extends DynamoDBTableCommandCommon
{
	//リクエスト
	use DynamoDBExpressionCommonTrait;
	use DynamoDBProjectionExpressionTrait;
	use DynamoDBRequestKeyTrait;
	
	//レスポンス
	use DynamoDBResponseConsumedCapacityTrait;
	
	//オプション
	use DynamoDBOptionConsumedCapacityTrait;
	use DynamoDBOptionReturnItemCollectionMetricsTrait;
	use DynamoDBOptionReturnValueTrait;
	
	const ID = "L804";
	const COMMAND_NAME = "GetItem";
	
	
	//----------------------------------------------------------------------------------
	// クエリ発行
	//----------------------------------------------------------------------------------
	
	/**
	 * リクエストの作成
	 * @return array
	 */
	public function createRequestParams()
	{
		
		//取得カラム指定を取得
		$projection_expression = $this->getProjectionExpression();
		
		//-------------------
		// パラメータ作成
		//-------------------
		$request_params = [
			'TableName' => $this->_table_name,                              //テーブル名
			'Key' => $this->_marshaler->marshalItem($this->getKey()),            //キー
		];
		
		//取得カラム指定があれば（SELECT）
		if (!empty($projection_expression)) {
			$request_params['ProjectionExpression'] = $projection_expression;
			
			//Expressionをつける
			$this->setRequestParamsExpressionPlaceHolder($request_params);
		}
		
		
		//オプションをくっつける
		return array_merge($request_params, $this->_options);
	}
	
	/**
	 * リクエスト処理
	 * @param $request_params
	 * @return Promise
	 */
	protected function requestMainAsync($request_params)
	{
		return $this->_dynamodb->getItemAsync($request_params);
	}
	
	/**
	 * バリデーション
	 * @throws DynamoDB_RequestException
	 */
	public function validation()
	{
		
		if (empty($this->getKey())) {
			throw new DynamoDB_RequestException("Keyは必須です");
		}
	}
	
	/**
	 * Query発行
	 * @return bool
	 * @throws DynamoDB_RequestException
	 */
	public function exec()
	{
		$this->validation();
		return parent::exec();
	}
	
	/**
	 * Query発行
	 * @return PromiseInterface
	 * @throws DynamoDB_RequestException
	 */
	public function execAsync()
	{
		$this->validation();
		return parent::execAsync();
	}
	
	/**
	 * リクエスト成功
	 * @param $response
	 * @return array|bool
	 */
	protected function success($response)
	{
		
		//パース
		$result = false;
		if (!empty($response["Item"])) {
			$result = $this->_marshaler->unmarshalItem($this->_response["Item"]);
		}
		
		return $result;
	}
	
	/**
	 * リクエスト失敗
	 * @param DynamoDbException $e
	 * @return bool
	 * @throws DynamoDB_RequestException
	 */
	protected function failed($e)
	{
		switch ($e->getAwsErrorCode()) {
			case "ResourceNotFoundException":
				throw new DynamoDB_RequestException("テーブル・インデックス名が間違っています", 0, $e);
		}
		
		return false;
	}
	
}
