<?php

namespace App\Spiders;

use Symfony\Component\DomCrawler\Crawler;
use GuzzleHttp\Client;


/**
 * Spider to guard shared methods
 */
class BaseSpider
{
	private $httClient;

	function __construct()
	{
		$this->httClient = new Client;	
	}

	public function getEnvURL(string $env_name) : string
	{
		$url =  env($env_name);
		if (!$url) {
			throw new \Exception("{$env_name} not defined in .env file.");
		}
		if (filter_var($url, FILTER_VALIDATE_URL) === false) {
			throw new \Exception("\"{$url}\" is not a valid url.");
		}
		return $url;
	}

	function get(string $url, array $options=[])
	{
		return $this->request($url, 'GET', $options);
	}

	function post(string $url, array $options=[])
	{
		return $this->request($url, 'POST', $options);
	}

	function put(string $url, array $options=[])
	{
		return $this->request($url, 'PUT', $options);
	}

	function request(string $url, string $method, array $options=[])
	{
		$res = $this->httClient->request($method, $url, $options);
		if ($res->getStatuscode() !== 200) {
			return;
		}
		return $res->getBody()->getContents();		
	}

	function parse($toParse) : Crawler
	{
		return new Crawler($toParse);
	}

}