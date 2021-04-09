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
    'prefix' => 'v1/auth'
], function ($router) {
    Route::post('login', 'AuthController@login');
    Route::post('register', 'AuthController@register');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::get('user-profile', 'AuthController@userProfile');
});

Route::group([
    'middleware' => ['jwt.verify'],
    'prefix' => 'v1/inventory'
], function ($router) {
    // home
    Route::get('app-data', 'HomeController@appData');

    // counter
    Route::post('counter/create', 'CounterController@store');
    Route::get('counter/invoices', 'CounterController@index');

    // category
    Route::post('categories', 'CategoryController@store');
    Route::get('categories', 'CategoryController@showCategoryList');
    Route::get('categories/{id}', 'CategoryController@show');
    Route::put('categories/{id}', 'CategoryController@update');
    Route::delete('categories/{id}', 'CategoryController@destroy');

    // product
    Route::post('products', 'ProductController@store');
    Route::get('products', 'ProductController@index');
    Route::get('products/{id}', 'ProductController@show');
    Route::put('products/{id}', 'ProductController@update');
    Route::delete('products/{id}', 'ProductController@destroy');

    // order
    Route::post('orders', 'OrderController@store');
    Route::get('orders', 'OrderController@index');
    Route::get('orders/{id}', 'OrderController@show');
    Route::put('orders/{id}', 'OrderController@update');
    Route::delete('orders/{id}', 'OrderController@destroy');

    // brand
    Route::post('create', 'BrandController@store');
    Route::get('brands', 'BrandController@index');
    Route::get('brands/{id}', 'BrandController@show');
    Route::put('brands/{id}', 'BrandController@update');
    Route::delete('brands/{id}', 'BrandController@destroy');
});

