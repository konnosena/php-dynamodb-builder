<?php

namespace konnosena\DynamoDB\Command;

use GuzzleHttp\Promise\PromiseInterface;
use konnosena\DynamoDB\Common\DynamoDBGlobalCommandCommon;
use konnosena\DynamoDB\DynamoDBBuilder;
use Aws\DynamoDb\Exception\DynamoDbException;
use konnosena\DynamoDB\Exception\DynamoDB_RequestException;


/**
 * DynamoDBUnTagResourceBuilder
 * タグの関連付け外し設定
 */
class DynamoDBUnTagResourceBuilder extends DynamoDBGlobalCommandCommon
{
	const ID = "L804";
	const COMMAND_NAME = "UnTagResource";
	
	//タグ
	protected $_tag_keys = [];
	
	//リソース
	protected $_resource_arn = "";
	
	/**
	 * コマンド名取得
	 * @return string
	 */
	public function getCommand()
	{
		return "TagResource";
	}
	
	//----------------------------------------------------------------------------------
	// クエリ発行前
	//----------------------------------------------------------------------------------
	/**
	 * DynamoDBQuery constructor.
	 * @param DynamoDBBuilder $dynamodb
	 * @param string $resource_arn
	 */
	public function __construct(DynamoDBBuilder $dynamodb, $resource_arn)
	{
		parent::__construct($dynamodb);
		
		$this->_resource_arn = $resource_arn;
	}
	
	/**
	 * 削除するタグ
	 * @param $key
	 */
	public function addUnTagKey($key)
	{
		$this->_tag_keys[] = $key;
	}
	
	/**
	 * 削除するタグ一覧追加
	 * @param $key_list
	 */
	public function addUnTagKeys($key_list)
	{
		$this->_tag_keys = array_merge($this->_tag_keys, $key_list);
	}
	
	/**
	 * 削除するタグ一覧
	 * @param $key_list
	 */
	public function setUnTagKey($key_list)
	{
		$this->_tag_keys = $key_list;
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
			"ResourceArn" => $this->_resource_arn,
			"TagKeys" => $this->_tag_keys
		];
	}
	
	/**
	 * リクエスト処理
	 * @param $request_params
	 * @return PromiseInterface
	 */
	protected function requestMainAsync($request_params)
	{
		return $this->_dynamodb->untagResourceAsync($request_params);
	}

	/**
	 * バリデーション
	 * @throws DynamoDB_RequestException
	 */
	public function validation(){
		if (empty($this->_tag_keys)) {
			throw new DynamoDB_RequestException("タグが指定されていません");
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
			
			//テーブル・インデックスが無い
			case "ResourceNotFoundException":
				throw new DynamoDB_RequestException("テーブルが存在しません", 0, $e);
			
		}
		return false;
	}
}

