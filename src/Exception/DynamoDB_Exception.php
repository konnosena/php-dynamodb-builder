<?php

namespace konnosena\DynamoDB\Exception;

use Aws\CommandInterface;
use Aws\DynamoDb\Exception\DynamoDbException;
use Aws\ResultInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class DynamoDB_Exception
 * @package konnosena\DynamoDB\Exception
 * @property DynamoDbException $dynamodb_exception
 */
class DynamoDB_Exception extends \Exception
{
	private $dynamodb_exception = null;
	
	public function __construct($message = "", $code = 0, \Throwable $previous = null)
	{
		//DynamoDBExceptionの場合ラッパとする
		if ($previous instanceof DynamoDbException) {
			$this->dynamodb_exception = $previous;
			parent::__construct(!empty($message) ? $message : $this->dynamodb_exception->getMessage(), !empty($code) ? $code : $this->dynamodb_exception->getCode(), $previous);
		}
		else{
			parent::__construct($message, 0, $previous);
		}
	}
	
	
	/**
	 * Get the command that was executed.
	 *
	 * @return CommandInterface
	 */
	public function getCommand()
	{
		if(is_null($this->dynamodb_exception)){
			return null;
		}
		
		return $this->dynamodb_exception->getCommand();
	}
	
	/**
	 * Get the concise error message if any.
	 *
	 * @return string|null
	 */
	public function getAwsErrorMessage()
	{
		if(is_null($this->dynamodb_exception)){
			return null;
		}
	
		return $this->dynamodb_exception->getAwsErrorMessage();
	}
	
	/**
	 * Get the sent HTTP request if any.
	 *
	 * @return RequestInterface|null
	 */
	public function getRequest()
	{
		if(is_null($this->dynamodb_exception)){
			return null;
		}
	
		return $this->dynamodb_exception->getRequest();
	}
	
	/**
	 * Get the received HTTP response if any.
	 *
	 * @return ResponseInterface|null
	 */
	public function getResponse()
	{
		if(is_null($this->dynamodb_exception)){
			return null;
		}
	
		return $this->dynamodb_exception->getResponse();
	}
	
	/**
	 * Get the result of the exception if available
	 *
	 * @return ResultInterface|null
	 */
	public function getResult()
	{
		if(is_null($this->dynamodb_exception)){
			return null;
		}
	
		return $this->dynamodb_exception->getResult();
	}
	
	/**
	 * Returns true if this is a connection error.
	 *
	 * @return bool
	 */
	public function isConnectionError()
	{
		if(is_null($this->dynamodb_exception)){
			return false;
		}
	
		return $this->dynamodb_exception->isConnectionError();
	}
	
	/**
	 * If available, gets the HTTP status code of the corresponding response
	 *
	 * @return int|null
	 */
	public function getStatusCode()
	{
		if(is_null($this->dynamodb_exception)){
			return null;
		}
		return $this->dynamodb_exception->getStatusCode();
	}
	
	/**
	 * Get the request ID of the error. This value is only present if a
	 * response was received and is not present in the event of a networking
	 * error.
	 *
	 * @return string|null Returns null if no response was received
	 */
	public function getAwsRequestId()
	{
		if(is_null($this->dynamodb_exception)){
			return null;
		}
		return $this->dynamodb_exception->getAwsRequestId();
	}
	
	/**
	 * Get the AWS error type.
	 *
	 * @return string|null Returns null if no response was received
	 */
	public function getAwsErrorType()
	{
		if(is_null($this->dynamodb_exception)){
			return null;
		}
		return $this->dynamodb_exception->getAwsErrorType();
	}
	
	/**
	 * Get the AWS error code.
	 *
	 * @return string|null Returns null if no response was received
	 */
	public function getAwsErrorCode()
	{
		if(is_null($this->dynamodb_exception)){
			return null;
		}
		return $this->dynamodb_exception->getAwsErrorCode();
	}
	
	/**
	 * Get all transfer information as an associative array if no $name
	 * argument is supplied, or gets a specific transfer statistic if
	 * a $name attribute is supplied (e.g., 'retries_attempted').
	 *
	 * @param string $name Name of the transfer stat to retrieve
	 *
	 * @return mixed|null|array
	 */
	public function getTransferInfo($name = null)
	{
		if(is_null($this->dynamodb_exception)){
			return null;
		}
		return $this->dynamodb_exception->getTransferInfo($name);
	}
	
}