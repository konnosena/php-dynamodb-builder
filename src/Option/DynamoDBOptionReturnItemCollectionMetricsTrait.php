<?php
namespace konnosena\DynamoDB\Option;

/**
 * Created by PhpStorm.
 * User: K_Yamada
 * Date: 2018/04/02
 * Time: 14:10
 */

trait DynamoDBOptionReturnItemCollectionMetricsTrait{

	/**
	 * 統計情報を返すかどうか
	 * @param $bool $bool
	 * @return $this
	 */
	public function setIsReturnItemCollectionMetrics($bool){
		
		if($bool){
			$this->_options["ReturnItemCollectionMetrics"] = "SIZE";
		}
		else{
			unset($this->_options["ReturnItemCollectionMetrics"]);
		}
		
		return $this;
	}

}