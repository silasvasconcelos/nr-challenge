<?php

namespace App\Spiders;

use App\Models\Analyze;
use App\Models\File;

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
			$files_list[$k]['name'] = trim($link->text());
			$files_list[$k]['file'] = $file_base_url . $link->attr('href');
		}
		return $files_list;
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

	function run()
	{
		try {
			$page = 1;
			$num_pages = 1;
			$base_url = $this->getTarget() . '?pagina=%s&p_p_id=licitacoescnpqportlet_WAR_licitacoescnpqportlet_INSTANCE_BHfsvMBDwU0V';
			$checked_btn_last_pages = false;
			while ($page <= $num_pages) {
				$crawler = $this->parse($this->get( sprintf($base_url, $page) ));
				
				if (!$checked_btn_last_pages) {
					$btn_last_page = $crawler->filter("#formLicit ul.lfr-pagination-buttons li.last a")->first()->attr('onclick');
					$num_pages = intval(preg_replace('/\D/', '', $btn_last_page));
					$checked_btn_last_pages = true;
				}
				
				foreach ($crawler->filter('#formLicit tbody tr') as $row) {
					$this->save($this->extraxtRow($row));
				}
				
				$page += 1;
			}
		} catch (\Exception $e) {
			\Log::error($e);
		}
	}

}