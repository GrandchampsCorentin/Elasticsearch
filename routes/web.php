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

Route::get('/', function () {
    dd(App\cms_nacel_layouts_produits::search('Stafford London')->explain());
});

Route::get('/morgan', function () {
    dd(App\Models\Ville::search('*')->explain());
});

Route::get('/search', 'ElasticController@search')->name('search');
Route::get('/getIndex', 'ElasticController@getIndex')->name('getIndex');

Route::post('/getSerp', 'AjaxController@getSerp')->name('toES');

// test migration repo sur poleWEB
