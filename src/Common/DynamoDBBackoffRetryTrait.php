<?php
namespace konnosena\DynamoDB\Common;

use konnosena\DynamoDB\DynamoDBBuilder;

/**
 * DynamoDBのリトライ
 * @property DynamoDBBuilder $_dynamodb_builder
 */
trait DynamoDBBackoffRetryTrait
{
	protected $_do_fallback = false;
	protected $_retry_count = 0;
	protected $_max_retries = 0;
	
	/**
	 * カウントのリセット
	 */
	protected function clearRetryCount(){
		$this->_retry_count = 0;
		$this->_max_retries = $this->_dynamodb_builder->getMaxRetries();
	}
	
	/**
	 * 指数バックオフリトライ
	 * @return bool
	 */
	protected function backOffRetry(){
		//指数バックオフでリトライする
		if ($this->_max_retries > $this->_retry_count) {
			//リトライ
			usleep(pow(2, $this->_retry_count++) * 100 * 1000);
			return true;
		}
		
		return false;
	}
}
