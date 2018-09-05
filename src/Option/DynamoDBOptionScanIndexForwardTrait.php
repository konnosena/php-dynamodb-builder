<?php
namespace konnosena\DynamoDB\Option;

/**
 * Created by PhpStorm.
 * User: K_Yamada
 * Date: 2018/04/02
 * Time: 14:10
 */

trait DynamoDBOptionScanIndexForwardTrait{
	
	/**
	 * ソート
	 * @param bool $is_forward
	 * @return $this
	 */
	public function isForward($is_forward){
		
		if($is_forward){
			unset($this->_options["ScanIndexForward"]);
		}
		else{
			$this->_options["ScanIndexForward"] = false;
		}
		
		return $this;
	}

}