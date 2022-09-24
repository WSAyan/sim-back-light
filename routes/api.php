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
    Route::get('users', 'AuthController@showUsersList');
    Route::get('roles', 'AuthController@showRolesList');
});

Route::group([
    'middleware' => ['jwt.verify'],
    'prefix' => 'v1/inventory'
], function ($router) {
    // common
    Route::get('app-data', 'HomeController@appData');
    Route::get('dropdowns', 'HomeController@getDropdowns');

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
    Route::get('products', 'ProductController@showProductList');
    Route::get('products/{id}', 'ProductController@show');
    Route::put('products/{id}', 'ProductController@update');
    Route::delete('products/{id}', 'ProductController@destroy');

    // product options
    Route::post('product_options', 'ProductOptionController@store');
    Route::get('product_options', 'ProductOptionController@showProductOptionList');
    Route::get('product_options/{id}', 'ProductOptionController@show');
    Route::put('product_options/{id}', 'ProductOptionController@update');
    Route::delete('product_options/{id}', 'ProductOptionController@destroy');

    // order
    Route::post('orders', 'OrderController@store');
    Route::get('orders', 'OrderController@index');
    Route::get('orders/{id}', 'OrderController@show');
    Route::put('orders/{id}', 'OrderController@update');
    Route::delete('orders/{id}', 'OrderController@destroy');

    // brand
    Route::post('brands', 'BrandController@store');
    Route::get('brands', 'BrandController@showBrandsList');
    Route::get('brands/{id}', 'BrandController@show');
    Route::put('brands/{id}', 'BrandController@update');
    Route::delete('brands/{id}', 'BrandController@destroy');

    // image
    Route::post('images', 'ImageController@store');
    Route::get('images', 'ImageController@showImageList');
    Route::get('images/{id}', 'ImageController@show');
    Route::put('images/{id}', 'ImageController@update');
    Route::delete('images/{id}', 'ImageController@destroy');

    // collection
    Route::post('collections', 'CollectionController@store');
    Route::get('collections', 'CollectionController@showCollectionsList');
    Route::get('collections/{id}', 'CollectionController@show');
    Route::put('collections/{id}', 'CollectionController@update');
    Route::delete('collections/{id}', 'CollectionController@destroy');

    // user
    Route::post('users', 'UserController@store');
    Route::get('users', 'UserController@showUsers');
    Route::get('users/{id}', 'UserController@show');
    Route::put('users/{id}', 'UserController@update');
    Route::delete('users/{id}', 'UserController@destroy');

    // otp
    Route::post('otps', 'OTPController@store');
    Route::post('otps/{id}', 'OTPController@verify');
    Route::get('otps', 'OTPController@showItems');
    Route::get('otps/{id}', 'OTPController@show');
    Route::put('otps/{id}', 'OTPController@update');
    Route::delete('otps/{id}', 'OTPController@destroy');

    // contact
    Route::post('contacts', 'ContactController@store');
    Route::get('contacts', 'ContactController@showContacts');
    Route::get('contacts/{id}', 'ContactController@show');
    Route::put('contacts/{id}', 'ContactController@update');
    Route::delete('contacts/{id}', 'ContactController@destroy');
});
