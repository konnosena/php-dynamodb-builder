<?php
namespace konnosena\DynamoDB\Response;


/**
 * Trait DynamoDBResponseConsumedCapacityTrait
 */
trait DynamoDBResponseConsumedCapacityTrait{
	
	/**
	 * ConsumedCapacityを取得
	 * @return array
	 */
	public function getResponseConsumedCapacity()
	{
		return $this->_response["ConsumedCapacity"] ?? [];
	}

	
}