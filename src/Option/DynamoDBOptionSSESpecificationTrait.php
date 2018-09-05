<?php
namespace konnosena\DynamoDB\Option;

/**
 * Created by PhpStorm.
 * User: K_Yamada
 * Date: 2018/04/02
 * Time: 14:10
 */

trait DynamoDBOptionSSESpecificationTrait{
	
	/**
	 * SSESpecificationの有効・無効
	 * @param boolean $bool
	 * @return $this
	 */
	public function setSSESpecification($bool){
		$this->_options['SSESpecification']["Enabled"] = $bool;
		return $this;
	}

}