<?php

namespace konnosena\DynamoDB\Command;

use Aws\DynamoDb\Exception\DynamoDbException;
use function GuzzleHttp\Promise\coroutine;
use GuzzleHttp\Promise\PromiseInterface;
use konnosena\DynamoDB\Common\DynamoDBGlobalCommandCommon;

/**
 * DynamoDBListBackupsBuilder
 * バックアップ情報一覧
 */
class DynamoDBListBackupsBuilder extends DynamoDBGlobalCommandCommon
{
	const ID = "L804";
	const COMMAND_NAME = "ListBackups";
	
	protected $_table_name = "";
	protected $_time_range_lower_bound = "";
	protected $_time_range_upper_bound = "";
	
	//----------------------------------------------------------------------------------
	// クエリ発行前
	//----------------------------------------------------------------------------------
	
	/**
	 * 指定テーブルのバックアップを取得する
	 * @param string $table_name
	 * @return $this
	 */
	public function setTable($table_name)
	{
		$this->_table_name = $table_name;
		return $this;
	}
	
	/**
	 * 指定日時以降に作成されたヤツを取得する
	 * @param int|string $time
	 * @return $this
	 */
	public function setTimeRangeLowerBound($time)
	{
		$this->_time_range_lower_bound = $time;
		return $this;
	}
	
	/**
	 * 指定日時以前に作成されたヤツを取得する
	 * @param int|string $time
	 * @return $this
	 */
	public function setTimeRangeUpperBound($time)
	{
		$this->_time_range_upper_bound = $time;
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
		
		//-------------------
		// リクエストの発行
		//-------------------
		$response = [];
		
		if (!empty($this->_table_name)) {
			$response["TableName"] = $this->_table_name;
		}
		if (!empty($this->_time_range_lower_bound)) {
			$response["TimeRangeLowerBound"] = $this->_time_range_lower_bound;
		}
		if (!empty($this->_time_range_upper_bound)) {
			$response["TimeRangeUpperBound"] = $this->_time_range_upper_bound;
		}
		
		return $response;
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
					$result = (yield $this->_dynamodb->listBackups($request_params));
					
					//結果を一旦解析する
					$this->afterRequest($result);
					
					//BackupDescription
					if (!empty($result["BackupSummaries"])) {
						$response = array_merge($response, $result["BackupSummaries"]);
					}
					
					//ページネーションの処理
					$request_params['ExclusiveStartBackupArn'] = $result['LastEvaluatedBackupArn'];
					
				} while ($request_params['ExclusiveStartBackupArn']);
				
			}
			catch (DynamoDbException $e) {
				yield $e;
			}
			
			yield $response;
			
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

