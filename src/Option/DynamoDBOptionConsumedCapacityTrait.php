<?php
namespace konnosena\DynamoDB\Option;

/**
 * Created by PhpStorm.
 * User: K_Yamada
 * Date: 2018/04/02
 * Time: 14:10
 */

trait DynamoDBOptionConsumedCapacityTrait{
	
	/**
	 * キャパシティの取得
	 * @param $consumed_capacity_type
	 * @return $this
	 */
	public function setReturnConsumedCapacity($consumed_capacity_type)
	{
		$this->_options["ReturnConsumedCapacity"] = $consumed_capacity_type;
		return $this;
	}
	
	
}