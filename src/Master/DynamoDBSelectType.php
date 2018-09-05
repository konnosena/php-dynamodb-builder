<?php
namespace konnosena\DynamoDB\Master;

/**
 * Created by PhpStorm.
 * User: K_Yamada
 * Date: 2018/04/02
 * Time: 14:12
 */

class DynamoDBSelectType{
	
	const ALL = "ALL_ATTRIBUTES";
	const INDEX_ALL = "ALL_PROJECTED_ATTRIBUTES";
	const COUNT = "COUNT";
	const SPECIFIC_ATTRIBUTES = "SPECIFIC_ATTRIBUTES";

}