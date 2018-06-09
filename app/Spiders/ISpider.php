<?php

namespace App\Spiders;

/**
 * Interface to spiders 
 **/
interface ISpider {

	public function getTarget() : string;
	public function setTarget(string $target);

}