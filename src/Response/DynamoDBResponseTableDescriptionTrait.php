<?php
namespace konnosena\DynamoDB\Response;

/**
 * Trait DynamoDBTableDescriptionTrait
 */
trait DynamoDBResponseTableDescriptionTrait{
	
	/**
	 * TableDescriptionを取得
	 * @return array
	 */
	public function getResponseTableDescription()
	{
		return $this->_response["TableDescription"] ?? ($this->_response["Table"] ?? []);
	}

	
}