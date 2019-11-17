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
Route::get('logout', 'AuthController@logout');
Route::post('register', 'AuthController@register');
Route::get('articles-list', 'ArticleController@indexPublic');
Route::get('articles/category/{id}/{offset}/{limit}', 'ArticleController@category');
Route::get('articles/category/{id}', 'ArticleController@category');
Route::get('articles-detail/{article}', 'ArticleController@showPublic');
Route::get('articles/{id}/comments', ['uses' => 'ArticleController@comments']);
Route::post('complaints', 'ComplaintController@store');
Route::resource('comments', 'CommentController')->except(['create', 'edit', 'index']);
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
    Route::get('comments/recent/{offset}/{limit}', 'CommentController@recent');
    Route::resource('articles', 'ArticleController')->except(['create', 'edit']); // , 'index', 'show'
    Route::resource('categories', 'CategoryController')->except(['create', 'edit']);
    Route::resource('complaints', 'ComplaintController')->except(['store', 'create', 'edit']);
    Route::resource('programs', 'ProgramController')->except(['create', 'edit', 'update', 'store']);
    Route::resource('subscriptions', 'SubscriptionController')->except(['create', 'edit', 'update', 'store']);
    Route::resource('users', 'UserController')->except(['create', 'edit']);
});

Route::fallback(function(){
    return response()->json([
        'message' => 'Page Not Found'], 404);
});