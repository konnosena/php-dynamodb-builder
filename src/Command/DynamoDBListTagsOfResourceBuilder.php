<?php

namespace konnosena\DynamoDB\Command;

use function GuzzleHttp\Promise\coroutine;
use GuzzleHttp\Promise\PromiseInterface;
use konnosena\DynamoDB\DynamoDBBuilder;
use Aws\DynamoDb\Exception\DynamoDbException;
use konnosena\DynamoDB\Common\DynamoDBGlobalCommandCommon;


/**
 * DynamoDBListTagsOfResourceBuilder
 * タグ一覧
 */
class DynamoDBListTagsOfResourceBuilder extends DynamoDBGlobalCommandCommon
{
	const ID = "L804";
	const COMMAND_NAME = "ListTagsOfResource";
	
	protected $_resource_arn = "";
	
	//----------------------------------------------------------------------------------
	// クエリ発行前
	//----------------------------------------------------------------------------------
	/**
	 * DynamoDBQuery constructor.
	 * @param DynamoDBBuilder $dynamodb
	 * @param $resource_arn
	 */
	public function __construct(DynamoDBBuilder $dynamodb, $resource_arn)
	{
		parent::__construct($dynamodb);
		
		$this->_resource_arn = $resource_arn;
	}
	
	//----------------------------------------------------------------------------------
	// クエリ発行
	//----------------------------------------------------------------------------------
	/**
	 * リクエストの作成
	 * @return array
	 */
	public function createRequestParams()
	{
		
		return [
			"ResourceArn" => $this->_resource_arn
		];
	}
	
	/**
	 * リクエスト処理
	 * @param $request_params
	 * @return PromiseInterface
	 */
	protected function requestMainAsync($request_params)
	{
		return coroutine(function () use ($request_params) {
			
			$response = [];
			
			try {
				
				do {
					//クエリ発行
					$result = (yield $this->_dynamodb->listTagsOfResource($request_params));
					
					//結果を一旦解析する
					$this->afterRequest($result);
					
					//Tags
					if (!empty($result["Tags"])) {
						$response = array_merge($response, $result["Tags"]);
					}
					
					//ページネーションの処理
					$request_params['NextToken'] = $result['NextToken'];
					
				} while ($request_params['NextToken']);
				
				yield $response;
			}
			catch (DynamoDbException $e) {
				yield $e;
			}
			
		});
	}
	
	
	/**
	 * Query発行
	 * @return bool|array
	 */
	public function exec()
	{
		return parent::exec();
	}
	
	/**
	 * リクエスト成功
	 * @param $response
	 * @return array
	 */
	protected function success($response)
	{
		return $response;
	}
	
	/**
	 * リクエスト失敗
	 * @param DynamoDbException $e
	 * @return bool
	 */
	protected function failed($e)
	{
		return false;
	}
}

