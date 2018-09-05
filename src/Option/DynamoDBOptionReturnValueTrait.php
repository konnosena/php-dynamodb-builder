<?php
namespace konnosena\DynamoDB\Option;

use konnosena\DynamoDB\Master\DynamoDBReturnValueType;

/**
 * Created by PhpStorm.
 * User: K_Yamada
 * Date: 2018/04/02
 * Time: 14:10
 */

trait DynamoDBOptionReturnValueTrait{
	
	/**
	 * 更新前の数値を返すかどうか
	 * @param $return_value
	 * @return $this
	 */
	public function setReturnValue($return_value){
		
		//チェック
		switch ($return_value){
			case DynamoDBReturnValueType::NONE:
			case DynamoDBReturnValueType::ALL_OLD:
			case DynamoDBReturnValueType::UPDATED_NEW:
			case DynamoDBReturnValueType::UPDATED_OLD:
				$this->_options["ReturnValues"] = $return_value;
				break;
			default:
				//許可されてない
		}
		return $this;
	}
}