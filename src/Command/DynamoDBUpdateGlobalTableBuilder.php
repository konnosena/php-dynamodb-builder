<?php

namespace konnosena\DynamoDB\Command;

use Aws\DynamoDb\Exception\DynamoDbException;
use GuzzleHttp\Promise\PromiseInterface;
use konnosena\DynamoDB\Common\DynamoDBTableCommandCommon;
use konnosena\DynamoDB\Exception\DynamoDB_RequestException;
use konnosena\DynamoDB\Response\DynamoDBResponseGlobalTableDescriptionTrait;


/**
 * DynamoDBUpdateGlobalTableBuilder
 * Globalテーブルの削除・作成を一括で
 */
class DynamoDBUpdateGlobalTableBuilder extends DynamoDBTableCommandCommon
{
	use DynamoDBResponseGlobalTableDescriptionTrait;
	
	const ID = "L804";
	const COMMAND_NAME = "UpdateGlobalTable";
	
	//リージョン
	protected $_create_regions = [];
	protected $_delete_regions = [];
	
	//----------------------------------------------------------------------------------
	// クエリ発行前
	//----------------------------------------------------------------------------------
	
	
	/**
	 * 作成するリージョンを追加
	 * @param string $region
	 */
	public function addCreateRegion($region)
	{
		$this->_create_regions[] = $region;
	}
	
	/**
	 * 作成するリージョンを追加
	 * @param string[] $regions
	 */
	public function addCreateRegions($regions)
	{
		$this->_create_regions = array_merge($regions, $this->_create_regions);
	}
	
	/**
	 * 作成するリージョンをセット
	 * @param string[] $regions
	 */
	public function setCreateRegions($regions)
	{
		$this->_create_regions = $regions;
	}
	
	/**
	 * 削除するリージョンを追加
	 * @param string $region
	 */
	public function addDeleteRegion($region)
	{
		$this->_delete_regions[] = $region;
	}
	
	/**
	 * 削除するリージョンを追加
	 * @param string[] $regions
	 */
	public function addDeleteRegions($regions)
	{
		$this->_delete_regions = array_merge($regions, $this->_delete_regions);
	}
	
	/**
	 * 削除するリージョンをセット
	 * @param string[] $regions
	 */
	public function setDeleteRegions($regions)
	{
		$this->_delete_regions = $regions;
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
		//ReplicaUpdates
		$replica_updates = [];
		foreach ($this->_delete_regions as $region) {
			$replica_updates[] = [
				"Delete" => [
					"RegionName" => $region
				]
			];
		}
		foreach ($this->_create_regions as $region) {
			$replica_updates[] = [
				"Create" => [
					"RegionName" => $region
				]
			];
		}
		
		//-------------------
		// リクエストの作成
		//-------------------
		return [
			"GlobalTableName" => $this->_table_name,
			"ReplicaUpdates" => $replica_updates
		];
	}
	
	/**
	 * リクエスト処理
	 * @param $request_params
	 * @return PromiseInterface
	 */
	protected function requestMainAsync($request_params)
	{
		return $this->_dynamodb->updateGlobalTableAsync($request_params);
	}
	
	
	
	/**
	 * バリデーション
	 * @throws DynamoDB_RequestException
	 */
	public function validation()
	{
		if (empty($this->_create_regions) && empty($this->_delete_regions)) {
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
	protected function success($response)
	{
		return true;
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
			case "GlobalTableNotFoundException":
				throw new DynamoDB_RequestException("グローバルテーブルが存在しません", 0, $e);
			case "ReplicaAlreadyExistsException":
				throw new DynamoDB_RequestException("レプリカが既に存在します", 0, $e);
			case "ReplicaNotFoundException":
				throw new DynamoDB_RequestException("レプリカが存在しません", 0, $e);
			case "TableNotFoundException":
				throw new DynamoDB_RequestException("テーブルが存在しません", 0, $e);
		}
		
		return false;
	}
	
}

