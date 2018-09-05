<?php

namespace konnosena\DynamoDB\Command;

use GuzzleHttp\Promise\PromiseInterface;
use konnosena\DynamoDB\Common\DynamoDBTableCommandCommon;
use konnosena\DynamoDB\DynamoDBBuilder;
use Aws\DynamoDb\Exception\DynamoDbException;
use konnosena\DynamoDB\Exception\DynamoDB_RequestException;


/**
 * DynamoDBDescribeTimeToLiveBuilder
 * TTLの設定
 */
class DynamoDBUpdateTimeToLiveBuilder extends DynamoDBTableCommandCommon
{
	const ID = "L804";
	const COMMAND_NAME = "UpdateTimeToLive";
	
	//TTL
	private $_enabled_ttl;
	private $_attribute_name;
	
	
	//----------------------------------------------------------------------------------
	// クエリ発行前
	//----------------------------------------------------------------------------------
	
	/**
	 *  constructor.
	 * @param DynamoDBBuilder $dynamodb
	 * @param $table
	 * @param $attribute_name
	 * @param bool $enabled_ttl
	 */
	public function __construct(DynamoDBBuilder $dynamodb, $table, $attribute_name, $enabled_ttl = false)
	{
		parent::__construct($dynamodb, $table);
		
		$this->_enabled_ttl = $enabled_ttl;
		$this->_attribute_name = $attribute_name;
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
			"TableName" => $this->_table_name,
			"TimeToLiveSpecification" => [
				'AttributeName' => $this->_attribute_name,
				'Enabled' => $this->_enabled_ttl
			],
		];
	}
	
	/**
	 * リクエスト処理
	 * @param $request_params
	 * @return PromiseInterface
	 */
	protected function requestMainAsync($request_params)
	{
		return $this->_dynamodb->updateTimeToLiveAsync($request_params);
	}
	
	
	/**
	 * Query発行
	 * @return bool
	 */
	public function exec()
	{
		return parent::exec();
	}
	
	
	/**
	 * リクエスト成功
	 * @param $response
	 * @return bool
	 */
	protected function success($response){
		return true;
	}
	
	/**
	 * リクエスト失敗
	 * @param DynamoDbException $e
	 * @return bool
	 * @throws DynamoDB_RequestException
	 */
	protected function failed($e){
		
		switch ($e->getAwsErrorCode()) {
			case "ResourceNotFoundException":
				throw new DynamoDB_RequestException("テーブル・インデックスが存在しません", 0, $e);
		}
		
		return false;
	}
}

