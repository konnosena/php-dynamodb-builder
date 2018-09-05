<?php

namespace konnosena\DynamoDB\Common;

use Aws\Result;
use Aws\DynamoDb\Exception\DynamoDbException;
use GuzzleHttp\Promise\Promise;
use GuzzleHttp\Promise\PromiseInterface;
use konnosena\DynamoDB\DynamoDBExecutor;
use konnosena\DynamoDB\Exception\DynamoDB_RequestException;
use konnosena\DynamoDB\Master\DynamoDBErrorCode;
use konnosena\DynamoDB\Option\DynamoDBOptionConsumedCapacityTrait;
use konnosena\DynamoDB\Option\DynamoDBOptionReturnItemCollectionMetricsTrait;
use konnosena\DynamoDB\Option\DynamoDBOptionReturnValueTrait;
use konnosena\DynamoDB\RequestParams\DynamoDBConditionExpressionTrait;
use konnosena\DynamoDB\RequestParams\DynamoDBExpressionCommonTrait;
use konnosena\DynamoDB\RequestParams\DynamoDBRequestKeyTrait;
use konnosena\DynamoDB\Response\DynamoDBResponseAttributeTrait;
use konnosena\DynamoDB\Response\DynamoDBResponseConsumedCapacityTrait;
use konnosena\DynamoDB\Response\DynamoDBResponseItemCollectionMetricsTrait;


/**
 * DynamoDBFallbackableUpdateItemCommon
 * アイテム更新基底クラス
 */
abstract class DynamoDBFallbackableUpdateItemCommon extends DynamoDBTableCommandCommon
{
	//リクエスト
	use DynamoDBExpressionCommonTrait;
	use DynamoDBConditionExpressionTrait;
	use DynamoDBRequestKeyTrait;

	//レスポンス
	use DynamoDBResponseAttributeTrait;
	use DynamoDBResponseConsumedCapacityTrait;
	use DynamoDBResponseItemCollectionMetricsTrait;
	
	//オプション
	use DynamoDBOptionReturnValueTrait;
	use DynamoDBOptionConsumedCapacityTrait;
	use DynamoDBOptionReturnItemCollectionMetricsTrait;
	
	/**
	 * Executorに追加
	 * @param DynamoDBExecutor $executor
	 */
	public function addExecutor($executor){
		$executor->addQuery($this);
	}
	
	/**
	 * Query発行
	 * @return bool
	 * @throws DynamoDB_RequestException
	 */
	public function exec()
	{
		return parent::exec();
	}
	
	/**
	 * 成功
	 * @param Result $result
	 * @return bool
	 */
	protected function success($result)
	{
		return true;
	}

	/**
	 * Query発行
	 * @param DynamoDbException $e
	 * @return bool $e
	 * @throws DynamoDB_RequestException
	 */
	protected function failed($e)
	{
		switch ($e->getAwsErrorCode()){
			
			//条件に当てはまらなかったので失敗（だけど成功とする）
			case DynamoDBErrorCode::CONDITIONAL_CHECK_FAILED_EXCEPTION:
				return true;
				
			//テーブル・インデックスが無い
			case DynamoDBErrorCode::RESOURCE_NOT_FOUND_EXCEPTION:
				throw new DynamoDB_RequestException("テーブル・インデックスが存在しません", 0, $e);
			
			case DynamoDBErrorCode::PROVISIONED_THROUGHPUT_EXCEEDED_EXCEPTION:			//スループットオーバー
			case DynamoDBErrorCode::ITEM_COLLECTION_SIZE_LIMIT_EXCEEDED_EXCEPTION:		//ローカルセカンダリインデックスが10GB超えた
			case DynamoDBErrorCode::INTERNAL_SERVER_ERROR:	//サーバ内部エラー
			
		}
		
		return false;
	}
}

