<?php
namespace konnosena\DynamoDB\Command;

use GuzzleHttp\Promise\Promise;
use konnosena\DynamoDB\Common\DynamoDBGlobalCommandCommon;
use konnosena\DynamoDB\DynamoDBBuilder;
use Aws\DynamoDb\Exception\DynamoDbException;
use konnosena\DynamoDB\Exception\DynamoDB_RequestException;
use konnosena\DynamoDB\Master\DynamoDBErrorCode;
use konnosena\DynamoDB\Response\DynamoDBResponseBackupDescriptionTrait;


/**
 * DynamoDBDeleteBackupBuilder
 * バックアップの削除
 */
class DynamoDBDeleteBackupBuilder extends DynamoDBGlobalCommandCommon
{
	//レスポンス
	use DynamoDBResponseBackupDescriptionTrait;
	
	const ID = "L804";
	const COMMAND_NAME = "DeleteBackup";
	
	//バックアップ名
	protected $_backup_arn = "";

	//----------------------------------------------------------------------------------
	// クエリ発行前
	//----------------------------------------------------------------------------------
	/**
	 * DynamoDBQuery constructor.
	 * @param DynamoDBBuilder $dynamodb
	 * @param string $backup_arn
	 */
	public function __construct(DynamoDBBuilder $dynamodb, $backup_arn)
	{
		parent::__construct($dynamodb);
		
		$this->_backup_arn = $backup_arn;
	}

	//----------------------------------------------------------------------------------
	// クエリ発行
	//----------------------------------------------------------------------------------
	
	/**
	 * リクエストの作成
	 * @return array
	 */
	public function createRequestParams(){
		return [
			"BackupArn" => $this->_backup_arn
		];
	}
	
	/**
	 * リクエスト処理
	 * @param $request_params
	 * @return Promise
	 */
	protected function requestMainAsync($request_params)
	{
		return $this->_dynamodb->deleteBackupAsync($request_params);
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
			case "BackupNotFoundException":
				throw new DynamoDB_RequestException("指定したバックアップが見つかりません", 0, $e);
				
			//バックアップが使用中
			case "BackupInUseException":
			
			//同時に作業出来る限界を超えています
			case DynamoDBErrorCode::LIMIT_EXCEEDED_EXCEPTION:
		}
		
		return false;
	}
}

