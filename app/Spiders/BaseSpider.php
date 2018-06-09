<?php

namespace App\Spiders;

/**
 * Spider to guard shared methods
 */
class BaseSpider
{
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
}