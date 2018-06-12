<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Analyze;
use App\Jobs\WebCrawlerSspDFSpider;
use App\Jobs\WebCrawlerCnpqSpider;
class DashboardController extends Controller
{
    function index()
    {
		$last_crawlers = $this->getLastCrawlers();
        $total_crawlers = $this->getTotalCrawlers();
    	return view('dashboard.index', compact('last_crawlers','total_crawlers'));
    }

    function verify()
    {
    	$data = [];
    	foreach ($this->getLastCrawlers() as $k => $crawler) {
    		$data['crawlers'][$k]['origin'] = $crawler->origin;
    		$data['crawlers'][$k]['name'] = $crawler->name;
    		$data['crawlers'][$k]['object'] = str_limit($crawler->object, 30, '...');
    	}
    	$data['total'] = $this->getTotalCrawlers();
        $data['chart'] = $this->getArrayChart();
    	return $data;
    }

    function startCrawlers()
    {
        dispatch(new WebCrawlerSspDFSpider);
		dispatch(new WebCrawlerCnpqSpider);
    }

    function getLastCrawlers()
    {
    	return Analyze::orderBy('created_at', 'desc')->limit(10)->get();
    }

    function getTotalCrawlers()
    {
        return Analyze::count();
    }

    function getArrayChart()
    {
    	return Analyze::select([
            'origin',
            \DB::raw('( select count(*) from analyzes ai where ai.origin = analyzes.origin ) crawlers')
        ])
        ->groupBy('analyzes.origin')->pluck('crawlers', 'origin')->toArray();
    }
}
