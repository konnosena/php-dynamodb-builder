<?php

namespace konnosena\DynamoDB\Common;

use Aws\DynamoDb\DynamoDbClient;
use Aws\DynamoDb\Marshaler;
use konnosena\DynamoDB\DynamoDBBuilder;


/**
 * DynamoDBのコマンド基底クラス
 * @property DynamoDbClient $_dynamodb
 * @property DynamoDBBuilder $_dynamodb_builder
 */
abstract class DynamoDBCommandCommon
{
	//データをDynamoDB用に
	/**
	 * @var Marshaler $_marshaler
	 */
	protected $_marshaler = null;
	
	//オプション
	protected $_dynamodb = null;
	protected $_dynamodb_builder = null;
	
	//explain
	protected $_is_query_success = false;
	
	//オプション
	protected $_options = [];
	
	//リクエスト
	protected $_request_params = [];
	
	//リザルト
	protected $_response = [];
	
	//最終結果
	protected $_result = [];
	
	/**
	 * DynamoDBCommandCommon constructor.
	 * @param DynamoDBBuilder $_dynamodb_builder
	 */
	public function __construct(DynamoDBBuilder $_dynamodb_builder)
	{
		$this->_dynamodb = $_dynamodb_builder->getDynamoDBClient();
		$this->_dynamodb_builder = $_dynamodb_builder;
		$this->_marshaler = new Marshaler([
			'ignore_invalid' => false,
			'nullify_invalid' => true,
			'wrap_numbers' => false,
		]);
	}
	
	/**
	 * オプション
	 * @param $option
	 * @param $value
	 * @return $this
	 */
	public function setOption($option, $value)
	{
		$this->_options[$option] = $value;
		return $this;
	}
	
	/**
	 * リクエストの作成
	 * @return mixed
	 */
	abstract public function createRequestParams();
	
	
	/**
	 * コマンド実行
	 */
	abstract public function exec();
	
	
	/**
	 * コマンド実行
	 */
	abstract public function execAsync();
	
	
	//----------------------------------------------------------------------------------
	// クエリ発行後
	//----------------------------------------------------------------------------------
	/**
	 * リクエストを取得
	 * @return array
	 */
	public function getRequestParams()
	{
		return $this->_request_params;
	}
	
	/**
	 * 結果
	 * @return array
	 */
	public function getResponse()
	{
		return $this->_response;
	}
	
	/**
	 * 最終結果
	 * @return mixed
	 */
	public function getResult()
	{
		return $this->_result;
	}
	
	
	/**
	 * DynamoDBBuilder
	 * @return DynamoDBBuilder
	 */
	public function getDynamoDBBuilder()
	{
		return $this->_dynamodb_builder;
	}
	
}
