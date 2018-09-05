<?php
namespace konnosena\DynamoDB\Master;

/**
 * Created by PhpStorm.
 * User: K_Yamada
 * Date: 2018/04/02
 * Time: 14:12
 */

class DynamoDBStreamViewType{
	
	/**
	 * 変更された項目のキー属性のみがストリームに書き込まれます。
	 */
	const KEYS_ONLY = "KEYS_ONLY";
	
	/**
	 * アイテム全体が、変更後に表示されるので、ストリームに書き込まれます。
	 */
	const NEW_IMAGE = "NEW_IMAGE";
	
	/**
	 * 変更される前の項目全体がストリームに書き込まれます。
	 */
	const OLD_IMAGE = "OLD_IMAGE";
	
	/**
	 * アイテムの新しいアイテムイメージと古いアイテムイメージの両方がストリームに書き込まれます。
	 */
	const NEW_AND_OLD_IMAGES = "NEW_AND_OLD_IMAGES";

}
