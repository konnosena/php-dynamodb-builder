<?php
namespace konnosena\DynamoDB\Common;

use Aws\DynamoDb\Marshaler;


/**
 * DynamoDBResponseCommon
 */
class DynamoDBResponseCommon
{
	//実データ
	protected $_original = [];
	
	/**
	 * @var Marshaler $_marshaler
	 */
	protected $_marshaler = null;

	/**
	 * DynamoDBConsumedCapacity constructor.
	 * @param array $ary
	 */
	public function __construct($ary)
	{
		$this->_original = $ary;
		$this->_marshaler = new Marshaler([
			'ignore_invalid'  => false,
			'nullify_invalid' => true,
			'wrap_numbers'    => false,
		]);
	}
	
	/**
	 * 元データを取得する
	 * @return array
	 */
	public function toArray(){
		return $this->_original;
	}
	
}
