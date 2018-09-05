<?php
namespace konnosena\DynamoDB;

use Aws\DynamoDb\DynamoDbClient;
use Aws\Sdk;
use Aws\Sqs\SqsClient;
use konnosena\DynamoDB\Builder\DynamoDBAccountBuilder;
use konnosena\DynamoDB\Builder\DynamoDBBackupBuilder;
use konnosena\DynamoDB\Builder\DynamoDBGlobalTableBuilder;
use konnosena\DynamoDB\Builder\DynamoDBItemBuilder;
use konnosena\DynamoDB\Builder\DynamoDBTableBuilder;


/**
 * Class DynamoDBBuilder
 * @package konnosena\DynamoDB
 * @property DynamoDbClient $__dynamodb
 * @property SqsClient $_sqs
 * @property \Closure $_debug_func
 */
class DynamoDBBuilder{
	
	private $_dynamodb = null;
	private $_debug_func = null;
	
	//explainを出力するかどうか
	protected $_is_output_explain = false;
	
	//フォールバックするかどうか
	private $_sqs = null;
	private $_sqs_fallback_queue_url = null;
	private $_max_retries = 3;
	
	
	/**
	 * コンストラクタ
	 * @param DynamoDbClient $dynamodb
	 * @param SqsClient $sqs
	 * @param $sqs_url
	 * @param \Closure $debug_func
	 */
	public function __construct($dynamodb, $sqs, $sqs_url, $debug_func = null)
	{
		//DynamoDBを設定
		$this->_dynamodb = $dynamodb;
		$this->_sqs = $sqs;
		$this->_sqs_fallback_queue_url = $sqs_url;
		$this->_debug_func = $debug_func;
	}
	
	/**
	 * Executor
	 * @param string $sqs_url
	 * @return DynamoDBExecutor
	 */
	public function getExecutor($sqs_url = null) {
		return new DynamoDBExecutor($this->_sqs, $sqs_url ?? $this->_sqs_fallback_queue_url );
	}
	
	/**
	 * デバッグ追加する
	 * @param mixed $param
	 * @param string $key
	 */
	public function addDebugMessage($param, $key = "dynamodb") {
		if(!is_null($this->_debug_func)){
			$debug_func = $this->_debug_func;
			$debug_func($param, $key);
		}
	}
	
	
	/**
	 * アカウントのビルダ取得
	 * @return DynamoDBAccountBuilder
	 */
	public function accountBuilder(){
		return new DynamoDBAccountBuilder($this);
	}
	
	/**
	 * globalTableのビルダ取得
	 * @param $global_table_name
	 * @return DynamoDBGlobalTableBuilder
	 */
	public function globalTableBuilder($global_table_name){
		return new DynamoDBGlobalTableBuilder($this, $global_table_name);
	}
	
	/**
	 * テーブルのクエリビルダ
	 * @param $table_name
	 * @return DynamoDBTableBuilder
	 */
	public function tableBuilder($table_name){
		return new DynamoDBTableBuilder($this, $table_name);
	}
	
	
	/**
	 * アイテムのクエリビルダ
	 * @return DynamoDBItemBuilder
	 */
	public function itemBuilder(){
		return new DynamoDBItemBuilder($this);
	}
	
	/**
	 * バックアップのクエリビルダ
	 * @return DynamoDBBackupBuilder
	 */
	public function backupBuilder(){
		return new DynamoDBBackupBuilder($this);
	}
	
	/**
	 * Explainを出力
	 * @param bool $_is_output_explain
	 */
	public function setIsOutputExplain(bool $_is_output_explain)
	{
		$this->_is_output_explain = $_is_output_explain;
	}
	
	/**
	 * Explainを出力
	 * @return boolean
	 */
	public function getIsOutputExplain()
	{
		return $this->_is_output_explain;
	}
	
	/**
	 * リトライ回数を取得
	 * @return boolean
	 */
	public function getMaxRetries()
	{
		return $this->_max_retries;
	}
	
	
	/**
	 * DynamoDB
	 * @return DynamoDbClient
	 */
	public function getDynamoDBClient(){
		return $this->_dynamodb;
	}

	/**
	 * SQS
	 * @return SqsClient
	 */
	public function getSqsClient(){
		return $this->_sqs;
	}

	/**
	 * SQS
	 * @return SqsClient
	 */
	public function getSqsFallbackQueueUrl(){
		return $this->_sqs_fallback_queue_url;
	}
	
}