<?php
namespace konnosena\DynamoDB\RequestParams;

trait DynamoDBLimitTrait{
	
	//リミット
	protected $_limit = 0;
	
	/**
	 * リミット
	 * @param int $num
	 * @return $this
	 */
	public function limit($num){
		$this->_limit = $num;
		return $this;
	}
	
	
}