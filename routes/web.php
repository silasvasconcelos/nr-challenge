<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('/', ['as' => 'dashboard.index', 'uses' => 'DashboardController@index']);
Route::get('/verify', ['as' => 'dashboard.verify', 'uses' => 'DashboardController@verify']);
Route::post('/start-crawlers', ['as' => 'dashboard.start_crawlers', 'uses' => 'DashboardController@startCrawlers']);
