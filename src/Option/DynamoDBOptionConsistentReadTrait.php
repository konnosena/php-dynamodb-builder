<?php
namespace konnosena\DynamoDB\Option;

/**
 * Created by PhpStorm.
 * User: K_Yamada
 * Date: 2018/04/02
 * Time: 14:10
 */

trait DynamoDBOptionConsistentReadTrait{
	
	/**
	 * 強い整合性の読み込み
	 * @param bool $bool
	 * @return $this
	 */
	public function consistentRead($bool){
		$this->_options['ConsistentRead'] = $bool;
		return $this;
	}
	
}