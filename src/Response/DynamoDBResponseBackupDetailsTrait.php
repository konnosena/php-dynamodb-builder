<?php
namespace konnosena\DynamoDB\Response;

/**
 * Trait DynamoDBResponseBackupDetailsTrait
 */
trait DynamoDBResponseBackupDetailsTrait{
	
	/**
	 * BackupDetailsを取得
	 * @return array
	 */
	public function getResponseBackupDetails()
	{
		return $this->_response["BackupDetails"] ?? [];
	}

	
}