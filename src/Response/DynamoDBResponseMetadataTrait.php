<?php
namespace konnosena\DynamoDB\Response;

/**
 * Trait DynamoDBUpdateExpressionTrait
 */
trait DynamoDBResponseMetadataTrait{
	
	/**
	 * Metadataを取得
	 * @return array
	 */
	public function getResponseMetadata()
	{
		return $this->_response["@metadata"] ?? [];
	}

	
}