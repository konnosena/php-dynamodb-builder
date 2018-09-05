<?php
namespace konnosena\DynamoDB\Option;

use konnosena\DynamoDB\Master\DynamoDBStreamViewType;

/**
 * Created by PhpStorm.
 * User: K_Yamada
 * Date: 2018/04/02
 * Time: 14:10
 */

trait DynamoDBOptionStreamSpecificationTrait
{
	/**
	 * SSESpecificationの有効・無効
	 * @param bool $enabled
	 * @param string $stream_view_type
	 * @return $this
	 */
	public function setStreamSpecification($enabled, $stream_view_type = DynamoDBStreamViewType::NEW_IMAGE)
	{
		if ($enabled) {
			$this->_options['SSESpecification'] = [
				"StreamEnabled" => true,
				"StreamViewType" => $stream_view_type
			];
		}
		else {
			$this->_options['SSESpecification'] = [
				"StreamEnabled" => false
			];
		}
		
		return $this;
	}
	
}