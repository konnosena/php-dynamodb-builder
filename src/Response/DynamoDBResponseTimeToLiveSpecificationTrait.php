<?php
namespace konnosena\DynamoDB\Response;

/**
 * Trait DynamoDBResponseTimeToLiveSpecificationTrait
 */
trait DynamoDBResponseTimeToLiveSpecificationTrait{
	
	/**
	 * TimeToLiveSpecification
	 * @return array
	 */
	public function getResponseTimeToLiveSpecification()
	{
		return $this->_response["TimeToLiveSpecification"] ?? [];
	}

	
}