<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('login', 'AuthController@login');
    Route::post('register', 'AuthController@register');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::get('user-profile', 'AuthController@userProfile');
});

Route::group([
    'middleware' => ['jwt.verify'],
    'prefix' => 'counter'
], function ($router) {
    Route::post('create', 'CounterController@store');
    Route::get('invoices', 'CounterController@index');
});

Route::group([
    'middleware' => ['jwt.verify'],
    'prefix' => 'category'
], function ($router) {
    Route::post('create', 'CategoryController@store');
    Route::get('categories', 'CategoryController@index');
    Route::get('categories/{id}', 'CategoryController@show');
});

Route::group([
    'middleware' => ['jwt.verify'],
    'prefix' => 'product'
], function ($router) {
    Route::post('create', 'ProductController@store');
    Route::get('products', 'ProductController@index');
    Route::get('products/{id}', 'ProductController@show');
});

Route::group([
    'middleware' => ['jwt.verify'],
    'prefix' => 'order'
], function ($router) {
    Route::post('create', 'OrderController@store');
    Route::get('orders', 'OrderController@index');
    Route::get('orders/{id}', 'OrderController@show');
});

Route::group([
    'middleware' => ['jwt.verify'],
    'prefix' => 'brand'
], function ($router) {
    Route::post('create', 'BrandController@store');
    Route::get('brands', 'BrandController@index');
    Route::get('brands/{id}', 'BrandController@show');
});

Route::group([
    'middleware' => ['jwt.verify'],
    'prefix' => 'home'
], function ($router) {
    Route::get('', 'HomeController@index');
});
