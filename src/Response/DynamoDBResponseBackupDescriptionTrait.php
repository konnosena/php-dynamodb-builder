<?php
namespace konnosena\DynamoDB\Response;

/**
 * Trait DynamoDBResponseBackupDescriptionTrait
 */
trait DynamoDBResponseBackupDescriptionTrait{
	
	/**
	 * BackupDescriptionを取得
	 * @return array
	 */
	public function getResponseBackupDescription()
	{
		return $this->_response["BackupDescription"] ?? [];
	}

	
}