<?php
namespace konnosena\DynamoDB\Command;

use GuzzleHttp\Promise\PromiseInterface;
use konnosena\DynamoDB\Common\DynamoDBSearchCommon;


/**
 * DynamoDBScan
 */
class DynamoDBScanBuilder extends DynamoDBSearchCommon
{
	const ID = "L805";
	const COMMAND_NAME = "Scan";
	
	/**
	 * 並列数を設定
	 * @param $total_segments
	 * @param $segment
	 * @return DynamoDBScanBuilder
	 */
	public function segments($total_segments, $segment)
	{
		$this->_options["TotalSegments"] = $total_segments;
		$this->_options["Segment"] = $segment;
		return $this;
	}
	
	/**
	 * リクエスト部分の実装
	 * @param $request_params
	 * @return PromiseInterface
	 */
	protected function requestMainAsync($request_params)
	{
		//Queryを呼び出し
		return parent::requestMainFuncAsync("scan", $request_params);
	}
	
	
	/**
	 * バリデーション
	 */
	public function validation()
	{
	
	}
}
