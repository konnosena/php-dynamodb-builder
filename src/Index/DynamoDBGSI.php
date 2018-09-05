<?php
namespace konnosena\DynamoDB\Index;

use konnosena\DynamoDB\Option\DynamoDBOptionProvisionedThroughputTrait;

/**
 * Created by PhpStorm.
 * User: K_Yamada
 * Date: 2018/04/02
 * Time: 18:26
 */

class DynamoDBGSI extends DynamoDBLSI
{
	use DynamoDBOptionProvisionedThroughputTrait;
	
	protected $_options = [];
	
	/**
	 * LocalSecondaryIndexes constructor.
	 * @param string $_index_name
	 * @param int $read_capacity_units
	 * @param int $write_capacity_units
	 */
	public function __construct(string $_index_name, $read_capacity_units = 5, $write_capacity_units = 5)
	{
		parent::__construct($_index_name);
		$this->setProvisionedThroughput($read_capacity_units, $write_capacity_units);
	}
	
	/**
	 * 送信パラメータを取得する
	 * @return array
	 */
	public function getRequestParams()
	{
		return array_merge(parent::getRequestParams(), $this->_options);
	}
	
}