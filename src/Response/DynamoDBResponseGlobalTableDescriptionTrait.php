<?php
namespace konnosena\DynamoDB\Response;

/**
 * Trait DynamoDBGlobalTableDescriptionTrait
 */
trait DynamoDBResponseGlobalTableDescriptionTrait{
	
	/**
	 * GlobalTableDescriptionを取得
	 * @return array
	 */
	public function getResponseGlobalTableDescription()
	{
		return $this->_response["GlobalTableDescription"] ?? [];
	}

	
}