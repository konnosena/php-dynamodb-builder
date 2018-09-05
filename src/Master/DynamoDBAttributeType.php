<?php
namespace konnosena\DynamoDB\Master;


class DynamoDBAttributeType
{
	const STRING = "S";    //文字列
	const STRING_SET = "SS";    //文字列セット
	const NUMBER = "N";    //数値
	const NUMBER_SET = "NS";    //数値セット
	const BINARY = "B";    //バイナリ
	const BINARY_SET = "BS";    //バイナリセット
	const BOOL = "BOOL";    //Boolean
	const NULL = "NULL";    //Null
	const TYPE_LIST = "L";    //リスト
	const MAP = "M";    //マップ
}