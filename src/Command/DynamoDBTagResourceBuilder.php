<?php
namespace konnosena\DynamoDB\Command;

use GuzzleHttp\Promise\PromiseInterface;
use konnosena\DynamoDB\Common\DynamoDBGlobalCommandCommon;
use konnosena\DynamoDB\DynamoDBBuilder;
use Aws\DynamoDb\Exception\DynamoDbException;
use konnosena\DynamoDB\Exception\DynamoDB_RequestException;


/**
 * DynamoDBTagResourceBuilder
 * タグの関連付け設定
 */
class DynamoDBTagResourceBuilder extends DynamoDBGlobalCommandCommon
{
	const ID = "L804";
	const COMMAND_NAME = "TagResource";
	
	//タグ
	protected $_tags = [];
	
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
	 * 追加するタグ
	 * @param $key
	 * @param $value
	 */
	public function addTag($key, $value){
		$this->_tags[$key] = $value;
	}
	/**
	 * 追加するタグ
	 * @param $key_value_list
	 */
	public function addTags($key_value_list){
		$this->_tags = array_merge($this->_tags, $key_value_list);
	}
	/**
	 * 追加するタグ
	 * @param $key_value_list
	 */
	public function setTags($key_value_list){
		$this->_tags = $key_value_list;
	}

	//----------------------------------------------------------------------------------
	// クエリ発行
	//----------------------------------------------------------------------------------
	
	/**
	 * リクエストの作成
	 * @return array
	 */
	public function createRequestParams(){
	
		$tags = [];
		foreach ($this->_tags as $key => $value){
			$tags[] = [
				"Key" => $key,
				"value" => $value
			];
		}
		
		//-------------------
		// リクエストの作成
		//-------------------
		return [
			"ResourceArn" => $this->_resource_arn,
			"Tags" => $tags
		];

	}
	
	
	/**
	 * リクエスト処理
	 * @param $request_params
	 * @return PromiseInterface
	 */
	protected function requestMainAsync($request_params)
	{
		return $this->_dynamodb->tagResourceAsync($request_params);
	}
	
	
	/**
	 * バリデーション
	 * @throws DynamoDB_RequestException
	 */
	public function validation(){
		if(empty($this->_tags)){
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

