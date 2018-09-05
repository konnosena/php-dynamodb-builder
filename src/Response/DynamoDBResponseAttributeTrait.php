<?php
namespace konnosena\DynamoDB\Response;

/**
 * Trait DynamoDBResponseAttributeTrait
 */
trait DynamoDBResponseAttributeTrait{
	
	/**
	 * Attributeを取得
	 * @return array
	 */
	public function getResponseAttribute()
	{
		$response = $this->_response["Attributes"] ?? [];
		
		//整形して返す
		return $this->_marshaler->unmarshalItem($response);
	}

	
}