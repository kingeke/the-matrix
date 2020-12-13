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

Route::get('/', 'DashboardController@welcome')->name('welcome');

Route::post('/upload', 'DashboardController@upload')->name('upload');

Route::get('/bank-statement', 'DashboardController@bankStatement')->name('bank-statement');

Route::get('/credit-check/crc', 'DashboardController@creditCheckCRC')->name('credit-check-crc');

Route::get('/credit-check/crs', 'DashboardController@creditCheckCRS')->name('credit-check-crs');

