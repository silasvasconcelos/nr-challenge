<?php

namespace App\Spiders;

/**
 * Spider to consumer informations from .env = SPIDER_CNPQ
 */
class CnpqSpider extends BaseSpider implements ISpider
{
	private $target;
	
	public function __construct()
	{
		$this->target =  $this->getEnvURL('SPIDER_CNPQ');
	}	

	public function getTarget() : string {
		return $this->target;
	}

	public function setTarget(string $target ) {
		return $this->target = $target;
	}
}