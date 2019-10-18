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

Route::post('login', 'AuthController@login');
Route::post('register', 'AuthController@register');
Route::post('complaints', 'ComplaintController@store');
Route::post('programs', 'ProgramController@store');
Route::post('subscriptions', 'SubscriptionController@store');

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:api')->group(function () {
    Route::get('user', 'AuthController@details');
    Route::resource('articles', 'ArticleController')->except(['create', 'edit']);
    Route::resource('categories', 'CategoryController')->except(['create', 'edit']);
    Route::resource('complaints', 'ComplaintController')->except(['store', 'create', 'edit', 'update']);
    Route::resource('programs', 'ProgramController')->except(['create', 'edit', 'update']);
    Route::resource('subscriptions', 'SubscriptionController')->except(['create', 'edit', 'update', 'store']);
});

Route::fallback(function(){
    return response()->json([
        'message' => 'Page Not Found'], 404);
});