<?php namespace Panlogic\DONATE;
/**
* DONATE helper package by Panlogic Ltd.
*
* NOTICE OF LICENSE
*
* Licensed under the terms from Panlogic Ltd.
*
* @package DONATE
* @version 1.0.0
* @author Panlogic Ltd
* @license MIT
* @copyright (c) 2015, Panlogic Ltd
* @link http://www.panlogic.co.uk
*/

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class Donate {

	/**
	 * The version of this class
	 *
	 * @var string
	 */
	private $version = "1.0.0";

	/**
	 * A Guzzle HTTP Client object
	 *
	 * @var \GuzzleHttp\Client
	 */
	private $client;

	/**
	 * The default HTTP method
	 *
	 * @var string
	 */
	private $method = "POST";

	/**
	 * The Donate API key
	 *
	 * @var string
	 */
	private $apikey;

	/**
	 * The response format expected
	 *
	 * @var string
	 */
	private $responseFormat = "json";

	/**
	 * Guzzle request options
	 *
	 * @var array
	 */
	private $requestOptions = [
		'headers' 			=> '',
		'body'				=> '',
		'allow_redirects' 	=> false,
		'timeout'			=> '5',
	];

	/**
	 * The base URL part for the request
	 *
	 * @var string
	 */
	private $base = "http://api.donate-platform.com/";

	/**
	 * The end point part of the URL for the request
	 *
	 * @var string
	 */
	private $endpoint;

	/**
	 * Create a new Donate instance
	 *
	 * @return void
	 */
	public function __construct($config = array())
	{
		$this->apikey = $config['platform'] == 'live' ? $config['live_apikey'] : $config['test_apikey'];
		$this->requestOptions['headers'] = ['X-API-KEY' => $this->apikey];
		$this->client = new Client();
	}

	/**
	 * Return a Guzzle Object
	 *
	 * @return GuzzleHTTPClient
	 */
	public function getClient()
	{
		return $this->client;
	}

	/**
	 * Make a request
	 *
	 * @return Object
	 */
	private function call()
	{
		$requestOptions = [];
		foreach($this->requestOptions['body'] as $key=>$value)
		{
			$requestOptions[strtoupper($key)] = $value;
		}
		$this->requestOptions['body'] = $requestOptions;
		try
		{
			switch(strtolower($this->method))
			{
				case "get":
				try
				{
					$response = $this->getClient()->get($this->base . $this->endpoint, $this->requestOptions);
				}
				catch(RequestException $e)
				{
					return $e->getResponse();
				}
				break;

				case "post":
				try
				{
					$response = $this->getClient()->post($this->base . $this->endpoint, $this->requestOptions);
				}
				catch(RequestException $e)
				{
					return $e->getResponse();
				}
				break;
			}
			return $response;
		}
		catch(GuzzleHttp\Exception\BadResponseException $ex)
		{
			return $ex->getResponse()->getBody();
		}
	}

	/**
	 * Capture the response from a request
	 *
	 * @return Object
	 */
	private function response($response)
	{
		$result = new \stdClass();
		$body = $response->getBody();
		$result->statusCode = $response->getStatusCode();
		$result->reason = $response->getReasonPhrase();
		$result->json = '';
		if ($this->responseFormat == 'json')
		{
			$result->json = $response->json();
		}
		$result->xml = '';
		if ($this->responseFormat == 'xml')
		{
			$result->xml = $response->xml();
		}
		$result->body = $body;
		return $result;
	}

	/**
	 * Get the class version
	 *
	 * @return string
	 */
	public function getVersion()
	{
		return $this->version;
	}

	/**
	 * Get the end point (REST path)
	 *
	 * @return string
	 */
	public function getEndPoint()
	{
		return $this->endpoint;
	}

	/**
	 * Get the api key
	 *
	 * @return string
	 */
	public function getAPIKey()
	{
		return $this->apikey;
	}

	/**
	 * Set the response format, default is JSON
	 *
	 * @return void
	 */
	public function setResponseFormat($format = "json")
	{
		$this->responseFormat = $format;
	}
}