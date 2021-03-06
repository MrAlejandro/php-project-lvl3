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

Route::get('/', function () {
    return view('domains.new');
});

Route::resource('domains', 'DomainController')->only([
    'index', 'show', 'store'
]);

Route::resource('domains.checks', 'DomainCheckController')->only([
    'store'
]);
