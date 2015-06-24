<?php namespace Panlogic\DONATE;
/**
* DONATE helper package by Panlogic Ltd.
*
* NOTICE OF LICENSE
*
* Licensed under the terms from Panlogic Ltd.
*
* @package DONATE
* @version 1.0.7
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
	private $version = "1.0.7";

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
	private $live_base = "http://api.donate-platform.com/";

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
		try {
			switch(strtolower($this->method))
			{
				case "get":
					try {
						$response = $this->getClient()->get($this->base . $this->endpoint, $this->requestOptions);
					} catch(RequestException $e) {
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
			if (is_object($response)) {
				return $response;
			}
			return false;
		}
		catch(RequestException $ex) {
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
	 * Check an number is a valid number
	 *
	 * @param  array  $body
	 * @return Object
	 */
	public function isValidNumber(array $params = [])
	{
		$this->method = "GET";
		$this->endpoint = sprintf('sms/isValidNumber/%s/%s',$params['number'],isset($params['country_code']) ? $params['country_code'] : 'GB');
		return $this->response($this->call());
	}

	/**
	 * Check an number is a valid mobile number
	 *
	 * @param  array  $body
	 * @return Object
	 */
	public function isMobileNumber(array $params = [])
	{
		$this->method = "GET";
		$this->endpoint = sprintf('sms/isMobileNumber/%s/%s',$params['number'],isset($params['country_code']) ? $params['country_code'] : 'GB');
		return $this->response($this->call());
	}

	/**
	 * Return a national formatted number
	 *
	 * @param  array  $body
	 * @return Object
	 */
	public function getNationalNumber(array $params = [])
	{
		$this->method = "GET";
		$this->endpoint = sprintf('sms/nationalNumber/%s/%s',$params['number'],isset($params['country_code']) ? $params['country_code'] : 'GB');
		return $this->response($this->call());
	}

	/**
	 * Return a national formatted number
	 *
	 * @param  array  $body
	 * @return Object
	 */
	public function formatNumber(array $params = [])
	{
		$this->method = "GET";
		$this->endpoint = sprintf('sms/formatNumber/%s/%s/%s',$params['number'],$params['country'],isset($params['format']) ? $params['format'] : '');
		return $this->response($this->call());
	}

	/**
	 * Get all countries
	 *
	 * @param  array  $body
	 * @return Object
	 */
	public function getAllAddresses(array $params = [])
	{
		$this->method = "GET";
		$this->endpoint = 'address';
		return $this->response($this->call());
	}

	/**
	 * Get all zones
	 *
	 * @param  array  $body
	 * @return Object
	 */
	public function getAllZones(array $params = [])
	{
		$this->method = "GET";
		$this->endpoint = 'zone';
		return $this->response($this->call());
	}

	/**
	 * Get all countries
	 *
	 * @param  array  $body
	 * @return Object
	 */
	public function getAllCountries(array $params = [])
	{
		$this->method = "GET";
		$this->endpoint = 'country';
		return $this->response($this->call());
	}

	/**
	 * Get countries filtered
	 *
	 * @param  array  $body
	 * @return Object
	 */
	public function getAllCountriesFiltered(array $params = [])
	{
		$this->method = "POST";
		$this->requestOptions['body'] = $params;
		$this->endpoint = 'country/filter';
		return $this->response($this->call());
	}

	/**
	 * Get received SMS messages
	 *
	 * @param  array  $body
	 * @return Object
	 */
	public function getAllSMS(array $params = [])
	{
		$this->method = "GET";
		$this->endpoint = 'sms';
		return $this->response($this->call());
	}

	/**
	 * Get received SMS messages
	 *
	 * @param  array  $body
	 * @return Object
	 */
	public function getAllSMSFiltered(array $params = [])
	{
		$this->method = "POST";
		$this->requestOptions['body'] = $params;
		$this->endpoint = 'sms/filter';
		return $this->response($this->call());
	}

	/**
	 * Get received SMS messages
	 *
	 * @param  array  $body
	 * @return Object
	 */
	public function getAllMOSMS(array $params = [])
	{
		$this->method = "GET";
		$this->endpoint = 'mosms';
		return $this->response($this->call());
	}

	/**
	 * Update received SMS message
	 *
	 * @param  array  $body
	 * @return Object
	 */
	public function updateMo(array $params = [])
	{
		$this->method = "POST";
		$this->requestOptions['body'] = $params;
		$this->endpoint = sprintf('mosms/%s/update',$params['sms_mo_id']);
		return $this->response($this->call());
	}

	/**
	 * Get received SMS messages
	 *
	 * @param  array  $body
	 * @return Object
	 */
	public function getAllMOSMSFiltered(array $params = [])
	{
		$this->method = "POST";
		$this->requestOptions['body'] = $params;
		$this->endpoint = 'mosms/filter';
		return $this->response($this->call());
	}

	/**
	 * Get received DR SMS messages
	 *
	 * @param  array  $params
	 * @return Object
	 */
	public function getAllDRSMSFiltered(array $params = [])
	{
		$this->method = "POST";
		$this->requestOptions['body'] = $params;
		$this->endpoint = 'drsms/filter';
		return $this->response($this->call());
	}

	/**
	 * Send an SMS message
	 *
	 * @param  array  $body
	 * @return Object
	 */
	public function sendSMS(array $params = [])
	{
		$this->method = "POST";
		$this->endpoint = 'sms/fonix/smsSend';
		$this->requestOptions['body'] = $params;
		return $this->response($this->call());
	}

	/**
	 * Send a charged SMS message
	 *
	 * @param  array  $body
	 * @return Object
	 */
	public function sendChargedMobileSMS(array $params = [])
	{
		$this->method = "POST";
		$this->endpoint = 'sms/fonix/smsChargeMobile';
		$this->requestOptions['body'] = $params;
		return $this->response($this->call());
	}

	/**
	 * Send a charged SMS message
	 *
	 * @param  array  $body
	 * @return Object
	 */
	public function sendChargedSMS(array $params = [])
	{
		$this->method = "POST";
		$this->endpoint = 'sms/fonix/smsCharge';
		$this->requestOptions['body'] = $params;
		return $this->response($this->call());
	}

	/**
	 * Get causes filtered
	 *
	 * @param  array  $body
	 * @return Object
	 */
	public function getCausesFiltered(array $params = [])
	{
		$this->method = "POST";
		$this->requestOptions['body'] = $params;
		$this->endpoint = 'causes/filter';
		return $this->response($this->call());
	}

	/**
	 * Update cause
	 *
	 * @param  array  $params
	 * @return Object
	 */
	public function updateCause(array $params = [])
	{
		$this->method = "POST";
		$this->requestOptions['body'] = $params;
		$this->endpoint = sprintf('causes/%s/update',$params['cause_id']);
		return $this->response($this->call());
	}

	public function getCauseDescription(array $params = [])
	{
		$this->method = "POST";
		$this->requestOptions['body'] = $params;
		$this->endpoint = sprintf('/causes/%s/description',$params['cause_id']);
		return $this->response($this->call());
	}

	/**
	 * Get organisations filtered
	 *
	 * @param  array  $params
	 * @return Object
	 */
	public function getOrganisationsFiltered(array $params = [])
	{
		$this->method = "POST";
		$this->requestOptions['body'] = $params;
		$this->endpoint = 'organisations/filter';
		return $this->response($this->call());
	}

	/**
	 * Get Gift Aid value from amount
	 *
	 * @param  float  $param
	 * @return Object
	 */
	public function getGiftAidValue($param)
	{
		$this->method = "GET";
		$this->endpoint = 'giftaid/value/' . $param;
		return $this->response($this->call());
	}

	/**
	 * Get donors filtered
	 *
	 * @param  array  $params
	 * @return Object
	 */
	public function getDonorsFiltered(array $params = [])
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
	public function addTransaction(array $params = [])
	{
		$this->method = "POST";
		$this->requestOptions['body'] = $params;
		$this->endpoint = 'transactions';
		return $this->response($this->call());
	}

	/**
	 * Update a transaction
	 *
	 * @param  array  $body
	 * @return Object
	 */
	public function updateTransaction(array $params = [])
	{
		$this->method = "POST";
		$this->requestOptions['body'] = $params;
		$this->endpoint = sprintf('transactions/%s/update',$params['transaction_id']);
		return $this->response($this->call());
	}

	/**
	 * Get all transactions
	 *
	 * @param  array  $params
	 * @return Object
	 */
	public function getTransactionsFiltered(array $params = [])
	{
		$this->method = "POST";
		$this->requestOptions['body'] = $params;
		$this->endpoint = 'transactions/filter';
		return $this->response($this->call());
	}

	/**
	 * Add a URL Aliases
	 *
	 * @param  array  $body
	 * @return Object
	 */
	public function addUrlAlias(array $params = [])
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
	public function getAllURLAlias(array $params = [])
	{
		$this->method = "POST";
		$this->endpoint = 'urlalias';
		return $this->response($this->call());
	}

	/**
	 * Get a random short string for short link
	 *
	 * @return Object
	 */
	public function getLinkAliasShort(array $params = [])
	{
		$this->method = "POST";
		$this->endpoint = 'urlalias/short';
		return $this->response($this->call());
	}

	/**
	 * Get SMS Alias Links
	 *
	 * @return Object
	 */
	public function getSMSLinkFiltered(array $params = [])
	{
		$this->method = "POST";
		$this->requestOptions['body'] = $params;
		$this->endpoint = 'smsalias/filter';
		return $this->response($this->call());
	}

	public function userContactInstitution(array $params = [])
	{
		$this->method = "POST";
		$this->requestOptions['body'] = $params;
		$this->endpoint = sprintf('donors/%s/%s/contact',$params['user_id'],$params['institution_id']);
		return $this->response($this->call());
	}

	public function userCanGiftAid(array $params = [])
	{
		$this->method = "POST";
		$this->requestOptions['body'] = $params;
		$this->endpoint = sprintf('donors/%s/cangiftaid',$params['user_id']);
		return $this->response($this->call());
	}

	/**
	 * Send an email
	 *
	 * @return Object
	 */
	public function email(array $params = [])
	{
		$this->method = "POST";
		$this->requestOptions['body'] = $params;
		$this->endpoint = 'email';
		return $this->response($this->call());
	}

	/**
	 * Get organisations / causes based on term
	 *
	 * @return Object
	 */
	public function search(array $params = [])
	{
		$this->method = "POST";
		$this->endpoint = 'search';
		$this->requestOptions['body'] = $params;
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