<?php

use Illuminate\Http\Request;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('sneakers', 'ManageController@getAll');

Route::get('sneaker/{id}', 'ManageController@getOne');

Route::post('sneaker', 'ManageController@create');

Route::put('sneaker/{id}', 'ManageController@edit');

Route::delete('sneaker/{id}', 'ManageController@delete');

Route::post('size', 'ManageController@addSize');

Route::get('sizes', 'ManageController@getAllSizes');

//-------------------------------------------------
//Client



Route::get('cart', 'ClientController@cart');

Route::post('checkout', 'ClientController@checkout');