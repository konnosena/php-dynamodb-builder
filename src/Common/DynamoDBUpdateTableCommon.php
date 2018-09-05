<?php

namespace konnosena\DynamoDB\Common;

use Aws\Result;
use Aws\DynamoDb\Exception\DynamoDbException;
use GuzzleHttp\Promise\Promise;
use GuzzleHttp\Promise\PromiseInterface;
use konnosena\DynamoDB\Exception\DynamoDB_RequestException;
use konnosena\DynamoDB\Master\DynamoDBErrorCode;
use konnosena\DynamoDB\Response\DynamoDBResponseTableDescriptionTrait;


/**
 * DynamoDBUpdateTableCommon
 * テーブル更新基底クラス
 */
abstract class DynamoDBUpdateTableCommon extends DynamoDBTableCommandCommon
{
	//レスポンス
	use DynamoDBResponseTableDescriptionTrait;
	
	const ID = "L804";
	
	
	//----------------------------------------------------------------------------------
	// クエリ発行
	//----------------------------------------------------------------------------------
	
	/**
	 * リクエスト処理
	 * @param $request_params
	 * @return Promise
	 */
	protected function requestMainAsync($request_params)
	{
		return $this->_dynamodb->updateTableAsync($request_params);
	}
	
	/**
	 * Query発行
	 * @return bool
	 */
	public function exec()
	{
		return parent::exec();
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
		
		switch ($e->getAwsErrorCode()){
			
			//テーブル・インデックスが無い
			case DynamoDBErrorCode::RESOURCE_NOT_FOUND_EXCEPTION:
				throw new DynamoDB_RequestException("テーブル・インデックスが存在しません", 0, $e);
				
			/*
			case DynamoDBErrorCode::RESOURCE_IN_USE_EXCEPTION:		//現在使用中のテーブルです
			case DynamoDBErrorCode::LIMIT_EXCEEDED_EXCEPTION:		//現在作業中のテーブルです
			*/
		}
		
		return false;
	}
}

