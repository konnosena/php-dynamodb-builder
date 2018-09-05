<?php
namespace konnosena\DynamoDB\Response;

/**
 * Trait DynamoDBUpdateExpressionTrait
 */
trait DynamoDBResponseItemCollectionMetricsTrait{
	
	/**
	 * ItemCollectionMetricsを取得
	 * @return array
	 */
	public function getResponseItemCollectionMetrics()
	{
		$response = $this->_response["ItemCollectionMetrics"] ?? [];
		
		//ItemCollectionMetricsあれば整形して返す
		if (!empty($response["ItemCollectionKey"])) {
			$response["ItemCollectionKey"] = $this->_marshaler->unmarshalItem($response["ItemCollectionKey"]);
		}
	
		return $response;
	}
	
}