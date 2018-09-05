<?php
namespace konnosena\DynamoDB\Master;

/**
 * Created by PhpStorm.
 * User: K_Yamada
 * Date: 2018/04/02
 * Time: 14:12
 */

class DynamoDBReturnValueType{
	
	const NONE = "NONE";
	const ALL_OLD = "ALL_OLD";
	const UPDATED_OLD = "UPDATED_OLD";
	const UPDATED_NEW = "UPDATED_NEW";

}