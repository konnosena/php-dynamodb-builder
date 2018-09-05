<?php
namespace konnosena\DynamoDB\Response;

/**
 * Trait DynamoDBResponseTimeToLiveDescriptionTrait
 */
trait DynamoDBResponseTimeToLiveDescriptionTrait{
	
	/**
	 * TimeToLiveDescription
	 * @return array
	 */
	public function getResponseTimeToLiveDescription()
	{
		return $this->_response["TimeToLiveDescription"] ?? [];
	}

	
}