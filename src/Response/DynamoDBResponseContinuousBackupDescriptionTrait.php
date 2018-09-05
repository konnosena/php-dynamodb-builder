<?php
namespace konnosena\DynamoDB\Response;

/**
 * Trait DynamoDBResponseContinuousBackupDescriptionTrait
 */
trait DynamoDBResponseContinuousBackupDescriptionTrait{
	
	/**
	 * ContinuousBackupsStatusを取得
	 * @return bool
	 */
	public function getResponseIsContinuousBackups()
	{
		$description = $this->getResponseContinuousBackupsDescription();
		
		//ENABLEDなら成功
		if(isset($description["ContinuousBackupsStatus"])){
			return $description["ContinuousBackupsStatus"] == "ENABLED";
		}
		else{
			return false;
		}
	}

	/**
	 * ContinuousBackupsStatusを取得
	 * @return array
	 */
	public function getResponseContinuousBackupsDescription()
	{
		return $this->_response["ContinuousBackupsDescription"] ?? [];
	}
	
	
}