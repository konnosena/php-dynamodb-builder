<?php

namespace konnosena\DynamoDB\Common;

use Aws\DynamoDb\Exception\DynamoDbException;
use Aws\Result;
use GuzzleHttp\Promise\PromiseInterface;

/**
 * DynamoDBのGlobalに対してのコマンド基底クラス
 * @property \Closure[] $_after_success_functions
 * @property \Closure[] $_after_failed_functions
 * @property \Closure[] $_after_always_functions
 */
abstract class DynamoDBGlobalCommandCommon extends DynamoDBCommandCommon
{
	//実行後関数リスト
	protected $_after_success_functions = [];
	protected $_after_failed_functions = [];
	protected $_after_always_functions = [];
	
	protected $exception = null;
	
	const COMMAND_NAME = "";
	
	/**
	 * コマンド取得
	 * @return string
	 */
	public function getCommand()
	{
		return static::COMMAND_NAME;
	}
	
	/**
	 * リクエスト処理
	 * @return Result|array
	 * @throw DynamoDbException
	 */
	/*
	protected function startRequest()
	{
		try {
			//リクエスト取得
			$request_params = $this->createRequestParams();
			
			//リクエストパラメータ前処理
			$request_params = $this->beforeRequest($request_params);
			
			//リクエスト
			$response = $this->requestMain($request_params);
			
			//リクエストパラメータ後処理
			return $this->afterRequest($response);
		}
		catch (DynamoDbException $e) {
			$this->exception = $e;
			$this->_dynamodb_builder->addDebugMessage($e->getMessage(), "DynamoDBError");
			$this->_is_query_success = false;
			throw $e;
		}
	}
	*/
	
	/**
	 * リクエスト処理(非同期)
	 * @param \Closure(Result $result) $success
	 * @param \Closure(AwsException $exception) $error
	 * @return PromiseInterface
	 * @throw DynamoDbException
	 */
	protected function startRequestAsync($success, $failed)
	{
		$start = microtime(true);
		
		try {
			
			//リクエスト取得
			$request_params = $this->createRequestParams();
			
			//リクエストパラメータ前処理
			$request_params = $this->beforeRequest($request_params);
			
			//成功
			$then_success = function ($response) use ($success, $request_params, $start) {
				
				//リクエストパラメータをデバッグへ
				$this->_dynamodb_builder->addDebugMessage([
					"query" => $request_params,
					"time" => round(microtime(true) - $start, 5)
				], "dynamodb_request");
				
				$this->afterRequest($response);    //レスポンスの基本処理
				$this->_result = $success($response);    //成功時の処理;
				
				//実行後関数があれば実行する
				if (!empty($this->_after_success_functions)) {
					foreach ($this->_after_success_functions as $func) {
						$func($this);
					}
				}
				if (!empty($this->_after_always_functions)) {
					foreach ($this->_after_always_functions as $func) {
						$func($this);
					}
				}
				
				return $this;
			};
			
			//失敗
			$then_failed = function (DynamoDbException $exception) use ($failed, $request_params, $start) {
				
				//リクエストパラメータをデバッグへ
				$this->_dynamodb_builder->addDebugMessage([
					"query" => $request_params,
					"time" => round(microtime(true) - $start, 5)
				], "dynamodb_request");
				
				$this->exception = $exception;
				$this->_dynamodb_builder->addDebugMessage($exception->getMessage(), "dynamodb_error");
				$this->_is_query_success = false;
				$this->_result = $failed($exception);
				
				//実行後関数があれば実行する
				if (!empty($this->_after_failed_functions)) {
					foreach ($this->_after_failed_functions as $func) {
						$func($this);
					}
				}
				if (!empty($this->_after_always_functions)) {
					foreach ($this->_after_always_functions as $func) {
						$func($this);
					}
				}
				
				return $exception;
			};
			
			//リクエスト
			return $this->requestMainAsync($request_params)
				->then( function ($response){
					if($response instanceof \Exception){
						throw $response;
					}
					return $response;
				})
				->then($then_success, $then_failed);
		}
		catch (DynamoDbException $e) {
			$this->exception = $e;
			$this->_dynamodb_builder->addDebugMessage($e->getMessage(), "dynamodb_error");
			$this->_is_query_success = false;
			throw $e;
		}
	}
	
	
	/**
	 * リクエスト前処理
	 * @param $request_params
	 * @return mixed
	 */
	protected function beforeRequest($request_params)
	{
		//リクエストパラメータを保持
		$this->_request_params = $request_params;
		return $request_params;
	}
	
	/**
	 * リクエスト処理
	 * @param $request_params
	 * @throws DynamoDbException
	 * @return Result
	 */
//	abstract protected function requestMain($request_params);
	
	/**
	 * リクエスト処理
	 * @param $request_params
	 * @return PromiseInterface
	 */
	abstract protected function requestMainAsync($request_params);
	
	
	/**
	 * Query発行
	 * @return mixed
	 */
	public function exec()
	{
		$this->execAsync()->wait();
		return $this->getResult();
	}
	
	
	/**
	 * Query発行
	 * @return PromiseInterface
	 */
	public function execAsync()
	{
		return $this->startRequestAsync(function ($response) {
			return $this->success($response);
		}, function ($e) {
			return $this->failed($e);
		});
	}
	
	/**
	 * バリデーション
	 */
	public function validation()
	{
		//基本は特に処理はなし
	}
	
	/**
	 * リクエスト成功
	 * @param $response
	 * @return mixed
	 */
	abstract protected function success($response);
	
	/**
	 * リクエスト失敗
	 * @param $e
	 * @return mixed
	 */
	abstract protected function failed($e);
	
	
	/**
	 * Resultを解析する
	 * @param Result|Result[] $response
	 * @return Result|Result[]
	 */
	protected function afterRequest($response)
	{
		//結果を解析
		$this->_response = $response;
		
		//TODO ★EXPLAINテスト
		if ($this->_dynamodb_builder->getIsOutputExplain()) {
			
			$explain = array();
			$explain["sql"] = json_encode($this->_request_params);
			$explain["explain"] = [];
			$explain["time"] = 0;
			
			//バックトレースして呼び出し元を取得する
			$trace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 3);
			$trace = json_decode(json_encode($trace, JSON_UNESCAPED_UNICODE), true);
			$dba = $trace[1];
			$model = $trace[2];
			$explain["backtrace"]["dba"] = $dba;
			$explain["backtrace"]["model"] = $model;
			
			//デバグ
			$this->_dynamodb_builder->addDebugMessage($explain, "explain");
		}
		
		return $response;
	}
	
	/**
	 * @return DynamoDbException
	 */
	public function getException()
	{
		return $this->exception;
	}
	
	
	/**
	 * 成功Functionの作成
	 * @param \Closure $func
	 */
	public function addSuccessFunction($func)
	{
		$this->_after_success_functions[] = $func;
	}
	
	/**
	 * 失敗Functionの作成
	 * @param \Closure $func
	 */
	public function addFailedFunction($func)
	{
		$this->_after_success_functions[] = $func;
	}
	
	/**
	 * いつでもFunctionの作成
	 * @param \Closure $func
	 */
	public function addAlwaysFunction($func)
	{
		$this->_after_always_functions[] = $func;
	}
	
	/**
	 * 成功Functionのクリア
	 */
	public function clearSuccessFunction()
	{
		$this->_after_success_functions = [];
	}
	
	/**
	 * 失敗Functionのクリア
	 */
	public function clearFailedFunction()
	{
		$this->_after_success_functions = [];
	}
	
	/**
	 * いつでもFunctionのクリア
	 */
	public function clearAlwaysFunction()
	{
		$this->_after_always_functions = [];
	}
}
