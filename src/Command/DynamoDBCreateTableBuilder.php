<?php

namespace konnosena\DynamoDB\Command;

use GuzzleHttp\Promise\Promise;
use GuzzleHttp\Promise\PromiseInterface;
use konnosena\DynamoDB\Common\DynamoDBTableCommandCommon;
use konnosena\DynamoDB\DynamoDBBuilder;
use Aws\DynamoDb\Exception\DynamoDbException;
use konnosena\DynamoDB\Exception\DynamoDB_RequestException;
use konnosena\DynamoDB\Index\DynamoDBGSI;
use konnosena\DynamoDB\Index\DynamoDBLSI;
use konnosena\DynamoDB\Option\DynamoDBOptionProvisionedThroughputTrait;
use konnosena\DynamoDB\Option\DynamoDBOptionSSESpecificationTrait;
use konnosena\DynamoDB\Option\DynamoDBOptionStreamSpecificationTrait;
use konnosena\DynamoDB\RequestParams\DynamoDBAttributeDefinitionsTrait;
use konnosena\DynamoDB\Response\DynamoDBResponseTableDescriptionTrait;


/**
 * DynamoDBPut
 * キーの内容を完全に置き換える（Updateとは違うよ）
 * @property DynamoDBLSI[] $_local_secondary_indexes
 * @property DynamoDBGSI[] $_global_secondary_indexes
 */
class DynamoDBCreateTableBuilder extends DynamoDBTableCommandCommon
{
	//リクエストパラメータ
	use DynamoDBAttributeDefinitionsTrait;
	
	//レスポンス
	use DynamoDBResponseTableDescriptionTrait;
	
	//オプション
	use DynamoDBOptionProvisionedThroughputTrait;
	use DynamoDBOptionSSESpecificationTrait;
	use DynamoDBOptionStreamSpecificationTrait;
	
	const ID = "L804";
	const COMMAND_NAME = "CreateTable";
	
	//キー
	protected $_partition_key = "";
	protected $_sort_key = "";
	
	//インデックス
	protected $_local_secondary_indexes = [];
	protected $_global_secondary_indexes = [];
	
	
	//----------------------------------------------------------------------------------
	// クエリ発行前
	//----------------------------------------------------------------------------------
	/**
	 * DynamoDBQuery constructor.
	 * @param DynamoDBBuilder $dynamodb
	 * @param $table_name
	 * @param int $read_capacity_units
	 * @param int $write_capacity_units
	 */
	public function __construct(DynamoDBBuilder $dynamodb, $table_name, $read_capacity_units = 5, $write_capacity_units = 5)
	{
		parent::__construct($dynamodb, $table_name);
		
		//デフォルト設定
		$this->setProvisionedThroughput($read_capacity_units, $write_capacity_units);
	}
	
	/**
	 * キーの設定
	 * @param $partition_key
	 * @param $sort_key
	 * @return $this
	 */
	public function setKey($partition_key, $sort_key = "")
	{
		$this->_partition_key = $partition_key;
		$this->_sort_key = $sort_key;
		
		return $this;
	}
	
	/**
	 * LocalSecondaryIndexesの追加
	 * @param DynamoDBLSI $local_secondary_index
	 * @return $this
	 */
	public function addLSI(DynamoDBLSI $local_secondary_index)
	{
		$this->_local_secondary_indexes[] = $local_secondary_index;
		return $this;
	}

	/**
	 * GlobalSecondaryIndexesの追加
	 * @param DynamoDBGSI $global_secondary_index
	 * @return $this
	 */
	public function addGSI(DynamoDBGSI $global_secondary_index)
	{
		$this->_global_secondary_indexes[] = $global_secondary_index;
		return $this;
	}

	


	//----------------------------------------------------------------------------------
	// クエリ発行
	//----------------------------------------------------------------------------------
	
	/**
	 * リクエストの作成
	 * @return array
	 */
	public function createRequestParams(){
	
		//keySchemeを作成
		$key_schemas = [];
		
		//プライマリキー
		$key_schemas[] = [
			"AttributeName" => $this->_partition_key,
			"KeyType" => "HASH"
		];
		
		//ソートキーの指定があったら入れる
		if (!empty($this->_sort_key)) {
			$key_schemas[] = [
				"AttributeName" => $this->_sort_key,
				"KeyType" => "RANGE"
			];
		}
		
		//-------------------
		// リクエストの作成
		//-------------------
		
		$request_params = [
			"TableName" => $this->_table_name,
			"KeySchema" => $key_schemas,
			"AttributeDefinitions" => $this->getAttributeDefinitions()
		];
		
		//ローカルセカンダリインデックスがあればつける
		if(!empty($this->_local_secondary_indexes)){
			foreach ($this->_local_secondary_indexes as $lsi){
				$request_params["LocalSecondaryIndexes"][] = $lsi->getRequestParams();
			}
		}

		//グローバルセカンダリインデックスがあればつける
		if(!empty($this->_global_secondary_indexes)){
			foreach ($this->_global_secondary_indexes as $gsi){
				$request_params["GlobalSecondaryIndexes"][] = $gsi->getRequestParams();
			}
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
		return $this->_dynamodb->createTableAsync($request_params);
	}
	
	/**
	 * バリデーション
	 * @throws DynamoDB_RequestException
	 */
	public function validation(){
		//キーが有るか
		if (empty($this->_partition_key)) {
			throw new DynamoDB_RequestException("キーが指定されていません");
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
	 */
	protected function failed($e){
		
		/*
		switch ($e->getAwsErrorCode()){
			case DynamoDBErrorCode::RESOURCE_IN_USE_EXCEPTION:	//現在作業中のテーブルです
			case DynamoDBErrorCode::LIMIT_EXCEEDED_EXCEPTION:	//同時に作業出来る限界を超えています
		}
		*/
		
		return false;
	}
}

