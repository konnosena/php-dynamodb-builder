<?php
namespace konnosena\DynamoDB\Command;

use GuzzleHttp\Promise\PromiseInterface;
use konnosena\DynamoDB\Common\DynamoDBSearchCommon;
use konnosena\DynamoDB\RequestParams\DynamoDBKeyConditionExpressionTrait;


/**
 * DynamoDBのクエリ組み立て
 */
class DynamoDBQueryBuilder extends DynamoDBSearchCommon
{
	//リクエスト
	use DynamoDBKeyConditionExpressionTrait;
	
	const ID = "L804";
	const COMMAND_NAME = "Query";
	
	//----------------------------------------------------------------------------------
	// クエリ発行
	//----------------------------------------------------------------------------------
	
	/**
	 * リクエストの作成
	 * @return array
	 */
	public function createRequestParams()
	{
		$request_params = parent::createRequestParams();
		
		//追加
		$request_params["KeyConditionExpression"] = $this->getKeyConditionExpressionString();
		
		return $request_params;
	}
	
	/**
	 * リクエスト部分の実装
	 * @param $request_params
	 * @return PromiseInterface
	 */
	protected function requestMainAsync($request_params)
	{
		return parent::requestMainFuncAsync("query", $request_params);
	}
	
	
	
}
