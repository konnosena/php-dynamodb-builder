<?php
namespace konnosena\DynamoDB\Option;

/**
 * Created by PhpStorm.
 * User: K_Yamada
 * Date: 2018/04/02
 * Time: 14:10
 */

trait DynamoDBOptionProvisionedThroughputTrait{
	
	/**
	 * プロビジョニングされたスループットの設定
	 * @param int $read_capacity_units
	 * @param int $wright_capacity_units
	 * @return $this
	 */
	public function setProvisionedThroughput($read_capacity_units, $wright_capacity_units){
		$this->_options['ProvisionedThroughput'] = [
			"ReadCapacityUnits" => $read_capacity_units,
			"WriteCapacityUnits" => $wright_capacity_units,
		];
		return $this;
	}

}