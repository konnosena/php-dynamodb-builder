<?php

namespace konnosena\DynamoDB\Command;

use Aws\DynamoDb\Exception\DynamoDbException;
use GuzzleHttp\Promise\Promise;
use GuzzleHttp\Promise\PromiseInterface;
use konnosena\DynamoDB\Common\DynamoDBGlobalTableCommandCommon;
use konnosena\DynamoDB\Exception\DynamoDB_Exception;
use konnosena\DynamoDB\Exception\DynamoDB_RequestException;
use konnosena\DynamoDB\Response\DynamoDBResponseGlobalTableDescriptionTrait;


/**
 * DynamoDBCreateGlobalTableBuilder
 * Globalテーブルの作成
 * すでに存在しているテーブルを別のリージョンに展開する
 */
class DynamoDBCreateGlobalTableBuilder extends DynamoDBGlobalTableCommandCommon
{
	use DynamoDBResponseGlobalTableDescriptionTrait;
	
	const ID = "L804";
	const COMMAND_NAME = "CreateGlobalTable";
	
	//リージョン
	protected $_regions = [];
	

	
	//----------------------------------------------------------------------------------
	// クエリ発行前
	//----------------------------------------------------------------------------------
	
	/**
	 * リージョンを追加
	 * @param string $region
	 */
	public function addRegion($region)
	{
		$this->_regions[] = $region;
	}
	
	/**
	 * リージョンを追加
	 * @param string[] $regions
	 */
	public function addRegions($regions)
	{
		$this->_regions = array_merge($regions, $this->_regions);
	}
	
	/**
	 * リージョンをセット
	 * @param string[] $regions
	 */
	public function setRegions($regions)
	{
		$this->_regions = $regions;
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
			"GlobalTableName" => $this->_global_table_name
		];
		
		//リージョンを設定
		$replication_group = [];
		foreach ($this->_regions as $region) {
			$replication_group[] = [
				"RegionName" => $region
			];
		}
		$request_params["ReplicationGroup"] = $replication_group;
		
		//リクエストの作成
		return $request_params;
	}
	
	/**
	 * リクエスト処理
	 * @param $request_params
	 * @return Promise
	 */
	protected function requestMainAsync($request_params)
	{
		return $this->_dynamodb->createGlobalTableAsync($request_params);
	}
	
	/**
	 * バリデーション
	 * @throws DynamoDB_RequestException
	 */
	public function validation(){
		if (empty($this->_regions)) {
			throw new DynamoDB_RequestException("リージョンの設定をしてください。");
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
	 * @throws DynamoDB_Exception
	 */
	protected function failed($e){
		
		switch ($e->getAwsErrorCode()){
			case "TableNotFoundException":
				throw new DynamoDB_RequestException("テーブルが存在しません");
			case "GlobalTableAlreadyExistsException":
				throw new DynamoDB_RequestException("既に存在するグローバルテーブル名です");
			
			//case "LimitExceededException":	//同時に作業出来る限界を超えています
		}
		
		return false;
	}
}

