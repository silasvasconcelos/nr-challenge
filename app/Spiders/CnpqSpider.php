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
		parent::__construct();

		$this->target =  $this->getEnvURL('SPIDER_CNPQ');
	}

	public function getTarget() : string {
		return $this->target;
	}

	public function setTarget(string $target ) {
		return $this->target = $target;
	}

	function extraxtRow($row)
	{
		$row = $this->parse($row);
		$data['origin'] = 'CNPQ';
		$data['name'] = $row->filter('h4')->text();
		$data['object'] = $row->filter('.cont_licitacoes')->text();
		$dates = $row->filter('.data_licitacao span');
		$data['starting_date'] = $dates->first()->text();
		$data['published'] = $dates->last()->text();
		$data['files'] = $this->extractFiles($row);
		return $data;
	}

	function extractFiles($row)
	{
		$files_list = [];
		$file_base_url = 'http://www.cnpq.br/';
		$files = $row->filter('.download-list li a');
		foreach ($files as $k => $file) {
			$link = $this->parse($file);
			$files_list['files'][$k]['name'] = trim($link->text());
			$files_list['files'][$k]['file'] = $file_base_url . $link->attr('href');
		}
		return $files_list;
	}

	function save($data)
	{
		dd('CnpqSpider@save: ', $data);
	}

	function run()
	{
		$crawler = $this->parse($this->get($this->getTarget()));
		foreach ($crawler->filter('#formLicit tbody tr') as $row) {
			$this->save($this->extraxtRow($row));
		}
	}

}