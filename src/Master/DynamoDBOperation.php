<?php
namespace konnosena\DynamoDB\Master;

class DynamoDBOperation{
	
	const EQUAL = '=';
	const MORE = '>';
	const MORE_AND = '>=';
	const LESS = '<';
	const LESS_AND = '<=';
	const BETWEEN = 'BETWEEN';
	const BEGIN = 'BEGIN';
	
	//Put condition
	const EXIST = "EXIST";
	const NOT_EXIST = "NOT_EXIST";
	const TYPE_EQUAL = "TYPE";
	const CONTAINS = "CONTAINS";
	const SIZE_EQUAL = "SIZE =";
	const SIZE_MORE = "SIZE >";
	const SIZE_MORE_AND = "SIZE >=";
	const SIZE_LESS = "SIZE <";
	const SIZE_LESS_AND = "SIZE <=";
	
}