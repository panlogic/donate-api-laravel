<?php namespace Panlogic\DONATE;
/**
* DONATE helper package by Panlogic Ltd.
*
* NOTICE OF LICENSE
*
* Licensed under the terms from Panlogic Ltd.
*
* @package DONATE
* @version 1.0.1
* @author Panlogic Ltd
* @license MIT
* @copyright (c) 2015, Panlogic Ltd
* @link http://www.panlogic.co.uk
*/

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Stream\Stream;

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
		'exceptions'		=> true,
	];

	/**
	 * The base URL part for the request
	 *
	 * @var string
	 */
	private $base = "";

	/**
	 * The dev base URL
	 *
	 * @var string
	 */
	private $dev_base = "http://dev.api.donate-platform.com/";

	/**
	 * The live base URL
	 *
	 * @var string
	 */
	private $live_base = "http://dev.api.donate-platform.com/";

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
		$this->base = $config['platform'] == 'live' ? $this->live_base : $this->dev_base;
		$this->apikey = $config['platform'] == 'live' ? $config['live_apikey'] : $config['test_apikey'];
		$this->requestOptions['headers'] = ['X-API-KEY' => $this->apikey, 'Accept' => 'application/json', 'Content-Type' => 'application/json'];
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
		$response = [];
		if(is_array($this->requestOptions['body']))
		{
			foreach($this->requestOptions['body'] as $key=>$value)
			{
				$requestOptions[$key] = $value;
			}
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
					$this->requestOptions['body'] = json_encode($this->requestOptions['body']);
					try {
						$response = $this->getClient()->post($this->base . $this->endpoint, $this->requestOptions);
					} catch(Exception $e) {
						$response = $e->getResponse();
					}
				break;
			}
			if (is_object($response))
			{
				return $response;
			}
			return false;
		}
		catch(RequestException $ex)
		{
			return $ex->getResponse();
		}
	}

	/**
	 * Capture the response from a request
	 *
	 * @return Object
	 */
	private function response($response)
	{
		if(!$response)
		{
			return false;
		}
		$result = new \stdClass();
		$result->request = [
			'url' => $this->base . $this->endpoint,
			'method' => $this->method,
			'options' => $this->requestOptions
		];
		$body = $response->getBody();
		$result->statusCode = $response->getStatusCode();
		$result->reason = $response->getReasonPhrase();
		$result->json = '';
		$result->xml = '';
		$result->errors = '';
		if($result->statusCode == 200)
		{
			if ($this->responseFormat == 'json')
			{
				$result->json = $response->json();
			}
			if ($this->responseFormat == 'xml')
			{
				$result->xml = $response->xml();
			}
		} else {
			$result->errors = json_decode($response->getBody()->getContents());
		}
		$result->body = $body;
		return $result;
	}

	/**
	 * Get received SMS messages
	 *
	 * @param  array  $body
	 * @return Object
	 */
	public function getAllSMS($params = array())
	{
		$this->method = "POST";
		$this->endpoint = 'sms';
		return $this->response($this->call());
	}

	/**
	 * Get received SMS messages
	 *
	 * @param  array  $body
	 * @return Object
	 */
	public function getAllMOSMS($params = array())
	{
		$this->method = "POST";
		$this->endpoint = 'mosms';
		return $this->response($this->call());
	}

	/**
	 * Get received SMS messages
	 *
	 * @param  array  $body
	 * @return Object
	 */
	public function getAllMOSMSFiltered($params = array())
	{
		$this->method = "POST";
		$this->requestOptions['body'] = $params;
		$this->endpoint = 'mosms/filter';
		return $this->response($this->call());
	}

	/**
	 * Update received SMS message
	 *
	 * @param  array  $body
	 * @return Object
	 */
	public function updateMo($params = array())
	{
		$this->method = "POST";
		$this->requestOptions['body'] = $params;
		$this->endpoint = sprintf('mosms/%s/update',$params['sms_mo_id']);
		return $this->response($this->call());
	}

	/**
	 * Send an SMS message
	 *
	 * @param  array  $body
	 * @return Object
	 */
	public function sendSMS($params = array())
	{
		$this->method = "POST";
		$this->endpoint = 'sms/fonix/smsSend';
		return $this->response($this->call());
	}

	/**
	 * Send a charged SMS message
	 *
	 * @param  array  $body
	 * @return Object
	 */
	public function sendChargedSMS($params = array())
	{
		$this->method = "POST";
		$this->endpoint = 'sms/fonix/smsCharge';
		return $this->response($this->call());
	}

	/**
	 * Get causes filtered
	 *
	 * @param  array  $body
	 * @return Object
	 */
	public function getCausesFiltered($params = array())
	{
		$this->method = "POST";
		$this->requestOptions['body'] = $params;
		$this->endpoint = 'causes/filter';
		return $this->response($this->call());
	}

	public function getOrganisationsFiltered($params = array())
	{
		$this->method = "POST";
		$this->requestOptions['body'] = $params;
		$this->endpoint = 'organisations/filter';
		return $this->response($this->call());
	}

	public function getGiftAidValue($param)
	{
		$this->method = "GET";
		$this->endpoint = 'giftaid/value/' . $param;
		return $this->response($this->call());
	}

	public function getDonorsFiltered($params = array())
	{
		$this->method = "POST";
		$this->requestOptions['body'] = $params;
		$this->endpoint = 'donors/filter';
		return $this->response($this->call());
	}

	/**
	 * Add a transaction
	 *
	 * @param  array  $body
	 * @return Object
	 */
	public function addTransaction($params = array())
	{
		$this->method = "POST";
		$this->requestOptions['body'] = $params;
		$this->endpoint = 'transactions';
		return $this->response($this->call());
	}

	/**
	 * Add a URL Aliases
	 *
	 * @param  array  $body
	 * @return Object
	 */
	public function addUrlAlias($params = array())
	{
		$this->method = "POST";
		$this->requestOptions['body'] = $params;
		$this->endpoint = 'urlalias';
		return $this->response($this->call());
	}

	/**
	 * Get all URL Aliases
	 *
	 * @param  array  $body
	 * @return Object
	 */
	public function getAllURLAlias($params = array())
	{
		$this->method = "POST";
		$this->endpoint = 'urlalias';
		return $this->response($this->call());
	}

	public function getLinkAliasShort($params = array())
	{
		$this->method = "POST";
		$this->endpoint = 'urlalias/short';
		return $this->response($this->call());
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