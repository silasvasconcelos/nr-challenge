<?php

namespace App\Spiders;

use App\Models\Analyze;
use App\Models\File;

/**
 * Spider to consumer informations from .env = SPIDER_SSP_DF
 */
class SspDFSpider extends BaseSpider implements ISpider
{
	private $target;
	private $base_url;
	
	public function __construct()
	{
		parent::__construct();

		$this->target =  $this->getEnvURL('SPIDER_SSP_DF');
	}

	public function getTarget() : string {
		return $this->target;
	}

	public function setTarget(string $target ) {
		return $this->target = $target;
	}

	function extraxtRow($row)
	{
		$link_title = $row->filter('h3 > a');
		$data['origin'] = 'SSP';
		$data['name'] = trim(strip_tags($link_title->text()));
		$object =  $row->filter('.dm_description p');
		$data['object'] = trim($object->first()->text());
		$data['starting_date'] = trim(str_replace('DATA DE ABERTURA: ', '', $object->last()->text()));
		$data['published'] = trim($row->filter('.dm_details table tr td')->eq(1)->text());
		$id = md5($data['name']);
		$data['files'][$id]['name'] = $data['name'];
		$data['files'][$id]['file'] = $this->base_url . $link_title->attr('href');
		return $data;
	}

	function save($data)
	{
		$files = $this->fillFiles(array_pull($data,'files'));
		$hash = md5(json_encode($data, true));
		array_set($data, 'hash', $hash);
		$analyze = Analyze::firstOrNew(compact('hash'));
		if ($analyze->exists == false and ($analyze->fill($data))->save()) {
			$analyze->files()->saveMany($files);
		}
	}

	function consumeCategory($url)
	{
		$category_crawler = $this->parse($this->get($url))->filter('#dm_docs > div.dm_row');
		foreach ($category_crawler as $row) {
			$data = $this->extraxtRow($this->parse($row));
			if (!empty(array_filter($data))) {
				$this->save($data);
			}
		}
	}

	function run()
	{
		$start_url = $this->getTarget();
		$parse_url =  parse_url($start_url);
		$this->base_url = sprintf("%s://%s", $parse_url['scheme'], $parse_url['host']);
		$categories_crawler = $this->parse($this->get($start_url));

		$regex_category = '/\((.*)\)/m';
		foreach ($categories_crawler->filter('#dm_cats .dm_title a') as $link) {
			$crawler = $this->parse($link);
			$category_name = $crawler->text();
			$category_url = $crawler->attr('href');

			preg_match_all($regex_category, $category_name, $matches, PREG_SET_ORDER, 0);	
			$pages = intval(preg_replace('/\D/', '', array_get($matches,'0.0')));

			if ($pages > 0) {
				$this->consumeCategory($this->base_url . $category_url);
			}
		}
	}

}