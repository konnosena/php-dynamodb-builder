<?php
namespace konnosena\DynamoDB\Master;

/**
 * Created by PhpStorm.
 * User: K_Yamada
 * Date: 2018/04/02
 * Time: 14:12
 */

class DynamoDBConsumedCapacityType{
	
	//ConsumedCapacity
	const TYPE_CONSUMED_CAPACITY_INDEXES = "INDEXES";
	const TYPE_CONSUMED_CAPACITY_GET_ITEM = "GetItem";
	const TYPE_CONSUMED_CAPACITY_TOTAL = "TOTAL";
	const TYPE_CONSUMED_CAPACITY_NONE = "NONE";
	
}