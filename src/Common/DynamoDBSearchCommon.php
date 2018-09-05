<?php

namespace konnosena\DynamoDB\Common;

use Aws\DynamoDb\Exception\DynamoDbException;
use function GuzzleHttp\Promise\coroutine;
use GuzzleHttp\Promise\PromiseInterface;
use konnosena\DynamoDB\Exception\DynamoDB_RequestException;
use konnosena\DynamoDB\Master\DynamoDBSelectType;
use konnosena\DynamoDB\Option\DynamoDBOptionConsistentReadTrait;
use konnosena\DynamoDB\Option\DynamoDBOptionConsumedCapacityTrait;
use konnosena\DynamoDB\Option\DynamoDBOptionScanIndexForwardTrait;
use konnosena\DynamoDB\RequestParams\DynamoDBExpressionCommonTrait;
use konnosena\DynamoDB\RequestParams\DynamoDBFilterExpressionTrait;
use konnosena\DynamoDB\RequestParams\DynamoDBLimitTrait;
use konnosena\DynamoDB\RequestParams\DynamoDBProjectionExpressionTrait;
use konnosena\DynamoDB\Response\DynamoDBResponseConsumedCapacityTrait;
use function konnosena\DynamoDB\Test\echoArray;


/**
 * DynamoDBの検索系の基底クラス
 */
abstract class DynamoDBSearchCommon extends DynamoDBTableCommandCommon
{
	//リクエストパラメータ
	use DynamoDBExpressionCommonTrait;
	use DynamoDBFilterExpressionTrait;
	use DynamoDBProjectionExpressionTrait;
	use DynamoDBLimitTrait;
	
	//レスポンス
	use DynamoDBResponseConsumedCapacityTrait;
	
	//オプション
	use DynamoDBOptionConsistentReadTrait;
	use DynamoDBOptionConsumedCapacityTrait;
	use DynamoDBOptionScanIndexForwardTrait;
	
	const ID = "L804";
	
	//index
	protected $_index_name = "";
	
	//selectタイプ
	protected $_select = "";
	
	//取得タイプ
	protected $_is_get_all = true;
	
	/**
	 * インデックス名
	 * @param $index_name
	 * @param bool $get_only_index Index以外の
	 * @return $this
	 */
	public function setIndex($index_name, $get_only_index = false)
	{
		$this->_index_name = $index_name;
		
		//Indexだけならコレにする
		if ($get_only_index === true) {
			if (empty($this->columns)) {
				$this->_select = DynamoDBSelectType::INDEX_ALL;
			}
		}
		return $this;
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
		$request_params = [
			"TableName" => $this->_table_name
		];
		
		//インデックス名
		if (!empty($this->_index_name)) {
			$request_params["IndexName"] = $this->_index_name;
		}
		
		//取得カラム指定があれば（Countのときは必要ない）
		$projection_expression = $this->getProjectionExpression();
		if (!empty($projection_expression) && $this->_select !== DynamoDBSelectType::COUNT) {
			$request_params['ProjectionExpression'] = $projection_expression;
		}
		
		//フィルタの指定があったら指定
		$filter_expressions = $this->getFilterExpression();
		if (!empty($filter_expressions)) {
			$request_params['FilterExpression'] = $filter_expressions;
		}
		
		//プレイスホルダを設定する
		$this->setRequestParamsExpressionPlaceHolder($request_params);
		
		//limit
		if ($this->_limit > 0) {
			$request_params['Limit'] = max($this->_limit * 2, 1000);
		}
		
		//SelectType
		if (!empty($this->_select)) {
			$request_params["Select"] = $this->_select;
		}
		
		//オプションをくっつける
		return array_merge($request_params, $this->_options);
	}
	
	
	/**
	 * リクエスト部分の実装
	 * @param string $dynamodb_func_name
	 * @param $request_params
	 * @return \Aws\Result
	 */
	protected function requestMainFunc($dynamodb_func_name, $request_params)
	{
		$response = [
			"Items" => [],
			"ConsumedCapacity" => [],
			"Count" => 0,
			"ScannedCount" => [],
		];
		
		do {
			//クエリ発行
			$result = $this->_dynamodb->$dynamodb_func_name($request_params);
			
			//結果のパース
			if (!empty($result["Items"])) {
				foreach ($result["Items"] as $item) {
					$response["Items"][] = $this->_marshaler->unmarshalItem($item);
				}
			}
			
			//ConsumedCapacity
			if (!empty($result["ConsumedCapacity"])) {
				$response["ConsumedCapacity"][] = $result["ConsumedCapacity"];
			}
			
			//Count
			if (!empty($result["Count"])) {
				$response["Count"] = $result["Count"];
			}
			
			//ScannedCount
			if (!empty($result["ScannedCount"])) {
				$response["ScannedCount"] = $result["ScannedCount"];
			}
			
			//必要数取得できたかどうか
			if ($this->_limit > 0 && count($response) >= $this->_limit) {
				//取得OK
				$response = array_slice($response, 0, $this->_limit);
				break;
			}
			
			//ページネーションの処理
			$request_params['ExclusiveStartKey'] = $result['LastEvaluatedKey'];
			
		} while ($request_params['ExclusiveStartKey']);
		
		return $response;
	}
	
	
	/**
	 * リクエスト部分の実装
	 * @param string $dynamodb_func_name
	 * @param $request_params
	 * @return PromiseInterface
	 */
	protected function requestMainFuncAsync($dynamodb_func_name, $request_params)
	{
		return coroutine(function () use ($request_params, $dynamodb_func_name) {
			
			$response = [
				"Items" => [],
				"ConsumedCapacity" => [],
				"Count" => 0,
				"ScannedCount" => [],
			];
			
			try {
				
				do {
					//クエリ発行
					$result = (yield $this->_dynamodb->$dynamodb_func_name($request_params));
					
					//結果のパース
					if (!empty($result["Items"])) {
						foreach ($result["Items"] as $item) {
							$response["Items"][] = $this->_marshaler->unmarshalItem($item);
						}
					}
					
					//ConsumedCapacity
					if (!empty($result["ConsumedCapacity"])) {
						$response["ConsumedCapacity"][] = $result["ConsumedCapacity"];
					}
					
					//Count
					if (!empty($result["Count"])) {
						$response["Count"] = $result["Count"];
					}
					
					//ScannedCount
					if (!empty($result["ScannedCount"])) {
						$response["ScannedCount"] = $result["ScannedCount"];
					}
					
					//必要数取得できたかどうか
					if ($this->_limit > 0 && count($response) >= $this->_limit) {
						//取得OK
						$response = array_slice($response, 0, $this->_limit);
						break;
					}
					
					//ページネーションの処理
					$request_params['ExclusiveStartKey'] = $result['LastEvaluatedKey'];
					
				} while ($request_params['ExclusiveStartKey']);
				
				yield $response;
			}
			catch (DynamoDbException $e) {
				yield $e;
			}
		});
	}
	
	
	/**
	 * バリデーション
	 * @throws DynamoDB_RequestException
	 */
	public function validation()
	{
		//パーティションキーは必須
		if (empty($this->_partition_key_condition)) {
			throw new DynamoDB_RequestException("パーティションキーは必須です");
		}
	}
	
	
	/**
	 * リクエスト成功
	 * @param $response
	 * @return array|bool
	 */
	protected function success($response)
	{
		if ($this->_is_get_all) {
			return $response["Items"];
		}
		else {
			return $response["Items"][0] ?? false;
		}
	}
	
	/**
	 * リクエスト失敗
	 * @param DynamoDbException $e
	 * @return bool
	 * @throws DynamoDB_RequestException
	 */
	protected function failed($e)
	{
		switch ($e->getAwsErrorCode()) {
			
			//バリデーションエラー
			case "ValidationException":
				throw new DynamoDB_RequestException("クエリが間違っています", 0, $e);
				
			//テーブル・インデックスが無い
			case "ResourceNotFoundException":
				throw new DynamoDB_RequestException("テーブル・インデックスが存在しません", 0, $e);
		}
		
		return false;
	}
	
	
	//--------------------------------------------------------------------
	// 複数行取得
	//--------------------------------------------------------------------
	
	
	/**
	 * Query発行
	 * @return bool|array
	 * @throws DynamoDB_RequestException
	 */
	public function exec()
	{
		$this->validation();
		
		//複数行取得
		$this->_is_get_all = true;
		
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
		
		//複数行取得
		$this->_is_get_all = true;
		
		return parent::execAsync();
	}
	
	
	
	
	//--------------------------------------------------------------------
	// 1行取得
	//--------------------------------------------------------------------
	/**
	 * 1行取得する
	 * @return bool|array
	 * @throws DynamoDB_RequestException
	 */
	public function execRow()
	{
		$this->execRowAsync()->wait();
		return $this->getResult();
	}
	
	/**
	 * 1行取得する
	 * @return PromiseInterface
	 * @throws DynamoDB_RequestException
	 */
	public function execRowAsync()
	{
		$this->validation();
		
		//複数行取得
		$this->_is_get_all = false;
		return parent::execAsync();
	}
	
	
	//--------------------------------------------------------------------
	// カウント取得
	//--------------------------------------------------------------------
	
	/**
	 * カウントを取得する
	 * @return int
	 * @throws DynamoDB_RequestException
	 */
	public function execCount()
	{
		$this->execCountAsync()->wait();
		return $this->getResult();
	}
	
	/**
	 * カウントを取得する
	 * @return PromiseInterface
	 * @throws DynamoDB_RequestException
	 */
	public function execCountAsync()
	{
		
		$this->validation();
		
		//カウント取得する
		$temp = $this->_select;
		$this->_select = DynamoDBSelectType::COUNT;
		
		//実行
		return $this->startRequestAsync(function ($response) use ($temp) {
			//成功
			$this->_select = $temp;
			return $response["Count"] ?? 0;
			
		}, function ($e) use ($temp) {
			//失敗
			$this->_select = $temp;
			return false;
		});
		
	}
	
	
	//--------------------------------------------------------------------
	// レスポンス取得
	//--------------------------------------------------------------------
	
	/**
	 * Itemの取得
	 * @return int
	 */
	public function getResponseItem()
	{
		return $this->_response["Item"] ?? 0;
	}
	
	/**
	 * Countの取得
	 * @return int
	 */
	public function getResponseCount()
	{
		return $this->_response["Count"] ?? 0;
	}
	
	/**
	 * ScannedCountの取得
	 * @return int
	 */
	public function getResponseScannedCount()
	{
		return $this->_response["ScannedCount"] ?? 0;
	}
	
	
}
