<?php
namespace konnosena\DynamoDB\Master;


use Aws\DynamoDb\Exception\DynamoDbException;

class DynamoDBExceptionType
{
	/**
	 * AWSの認証キーが無効
	 */
	const UNRECOGNIZED_CLIENT_EXCEPTION						= "UnrecognizedClientException";
	
	/**
	 * AWSの認証キーが間違ってる
	 */
	const MISSING_AUTHENTICATION_TOKEN_EXCEPTION			= "MissingAuthenticationTokenException";
	
	/**
	 * アクセス拒否された（署名ミス）
	 */
	const ACCESS_DENIED_EXCEPTION							= "AccessDeniedException";
	
	/**
	 * 署名ミス
	 */
	const INCOMPLETE_SIGNATURE_EXCEPTION					= "IncompleteSignatureException";
	
	/**
	 * 存在しないテーブルを指定した
	 */
	const RESOURCE_NOT_FOUND_EXCEPTION						= "ResourceNotFoundException";
	
	/**
	 * バリデーションエラー
	 */
	const VALIDATION_EXCEPTION								= "ValidationException";
	
	/**
	 * 条件付き更新失敗
	 */
	const CONDITIONAL_CHECK_FAILED_EXCEPTION				= "ConditionalCheckFailedException";
	
	/**
	 * プロビジョンドスループットを超えてる（リトライしてもだめなときはコレが出る）※再試行○
	 */
	const PROVISIONED_THROUGHPUT_EXCEEDED_EXCEPTION			= "ProvisionedThroughputExceededException";
	
	/**
	 * テーブルの作業中が10を超えています（CREATING DELETING UPDATINGが10個以上あるので少し待つ）※再試行○
	 */
	const LIMIT_EXCEEDED_EXCEPTION							= "LimitExceededException";
	
	/**
	 * テーブル系コマンドが早すぎる　※再試行○
	 */
	const THROTTLING_EXCEPTION								= "ThrottlingException";
	
	/**
	 * LSIの10GB制限を突破した
	 */
	const ITEM_COLLECTION_SIZE_LIMIT_EXCEEDED_EXCEPTION		= "ItemCollectionSizeLimitExceededException";
	
	/**
	 * テーブルが作成中なのに再作成、削除しようとした
	 */
	const RESOURCE_IN_USE_EXCEPTION							= "ResourceInUseException";
	
	/*
	 * サーバエラー
	 */
	const SERVER_EXCEPTION								 	= "ServerException";
	
	
	/**
	 * エラータイプを判定
	 * @param DynamoDbException $e
	 * @param $type
	 * @return bool
	 */
	protected function checkErrorType(DynamoDbException $e, $type)
	{
		//エラー（400番台）
		if($e->getStatusCode() >= 400 && $e->getStatusCode() < 500){
			return $e->getAwsErrorCode() == $type;
		}
		//サーバエラー系（500番台）
		else if($e->getStatusCode() >= 500 && $e->getStatusCode() < 600){
			return $type == self::SERVER_EXCEPTION;
		}
		//それ以外は無い
		else{
			return false;
		}
		
	}
}