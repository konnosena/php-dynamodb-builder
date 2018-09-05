<?php
namespace konnosena\DynamoDB\Master;

/**
 * Created by PhpStorm.
 * User: K_Yamada
 * Date: 2018/04/02
 * Time: 14:12
 */

class DynamoDBProjectionType
{
	/**
	 * Indexのみ
	 */
	const TYPE_KEYS_ONLY = "KEYS_ONLY";
	
	/**
	 * 指定する
	 */
	const TYPE_INCLUDE = "INCLUDE";
	
	/**
	 * すべて含む
	 */
	const TYPE_ALL = "ALL";
	
}