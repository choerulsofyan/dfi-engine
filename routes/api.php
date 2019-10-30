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
Route::get('articles', 'ArticleController@index');
Route::get('articles/category/{id}', 'ArticleController@category');
Route::get('articles/{article}', 'ArticleController@show');
Route::get('articles/{id}/comments', ['uses' => 'ArticleController@comments']);
Route::post('complaints', 'ComplaintController@store');
Route::post('comments', 'CommentController@store');
Route::post('programs', 'ProgramController@store');
Route::post('subscriptions', 'SubscriptionController@store');

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:api')->group(function () {
    Route::get('user', 'AuthController@details');
    Route::get('count/articles', 'ArticleController@countArticles');
    Route::get('count/comments', 'CommentController@countComments');
    Route::get('count/complaints', 'ComplaintController@countComplaints');
    Route::get('count/programs', 'ProgramController@countPrograms');
    Route::get('articles/recent/{count}', 'ArticleController@recent');
    Route::get('comments/recent/{count}', 'CommentController@recent');
    Route::resource('articles', 'ArticleController')->except(['create', 'edit', 'index', 'show']);
    Route::resource('categories', 'CategoryController')->except(['create', 'edit']);
    Route::resource('complaints', 'ComplaintController')->except(['store', 'create', 'edit', 'update']);
    Route::resource('programs', 'ProgramController')->except(['create', 'edit', 'update', 'store']);
    Route::resource('subscriptions', 'SubscriptionController')->except(['create', 'edit', 'update', 'store']);
    Route::resource('users', 'UserController')->except(['create', 'edit']);
});

Route::fallback(function(){
    return response()->json([
        'message' => 'Page Not Found'], 404);
});