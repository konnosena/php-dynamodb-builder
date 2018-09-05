<?php
namespace konnosena\DynamoDB\RequestParams;

/**
 * Trait DynamoDBUpdateExpressionTrait
 */
trait DynamoDBAttributeDefinitionsTrait
{
	//AttributeDefinitions
	protected $_attribute_definitions = [];
	
	/**
	 * AttributeDefinitionの設定
	 * @param string $attribute_name
	 * @param string $attribute_type
	 * @return $this
	 */
	public function setAttributeDefinition($attribute_name, $attribute_type){
		$this->_attribute_definitions[$attribute_name] = $attribute_type;
		return $this;
	}
	
	/**
	 * AttributeDefinitionの設定
	 * @param array $attribute_name_type_list
	 * @return $this
	 */
	public function setAttributeDefinitions($attribute_name_type_list){
		$this->_attribute_definitions = $attribute_name_type_list;
		return $this;
	}

	
	/**
	 * AttributeDefinitionsの取得
	 * @return array
	 */
	protected function getAttributeDefinitions(){
		//AttributeDefinitionを送信形式へ
		$attribute_definitions = [];
		foreach ($this->_attribute_definitions as $attribute_name => $type) {
			$attribute_definitions[] = [
				"AttributeName" => $attribute_name,
				"AttributeType" => $type
			];
		}
		
		return $attribute_definitions;
	}

}