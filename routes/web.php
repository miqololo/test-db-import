<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', 'HomeController@index');
Route::get('/info', 'HomeController@getInfo')->name('info');
Route::post('/models', 'HomeController@getModels')->name('models');
Route::post('/mark', 'HomeController@getMark')->name('mark');
Route::post('/cities', 'HomeController@getCities')->name('cities');
Route::post('/categories', 'HomeController@getCategories')->name('categories');

Route::get('upload-file', 'HomeController@import')->name('upload-file');
