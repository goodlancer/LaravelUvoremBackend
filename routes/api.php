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
Route::post('register', 'API\RegisterController@register');
Route::post('login', 'API\RegisterController@login');
Route::post('resetpassword', 'API\RegisterController@resetpassword');
Route::post('checkemail', 'API\RegisterController@checkemail');

Route::post('verification', 'API\RegisterController@verification');
Route::get('refreshServe', 'API\BaseController@refreshServe');
Route::get('checkLimitOffer', 'API\BaseController@checkLimitOffer');
Route::group(['middleware' => 'auth:api'], function () {
    Route::get ('logout', 'API\RegisterController@logout');

    // Article Api //republicOffer
    Route::post('insertArticle', 'API\ArticleController@insertArticle');
    Route::post('saveImage', 'API\ArticleController@saveImage');
    Route::get('republicOffer/{id}', 'API\ArticleController@republicOffer');
    Route::get('updateRating/{id}', 'API\ArticleController@updateRating');
    Route::post('getAllArticle', 'API\ArticleController@getAllArticle');
    Route::post('getAllOwnerArticle', 'API\ArticleController@getAllOwnerArticle');
    Route::post('getAticleList', 'API\ArticleController@getAticleList');
    Route::post('getAticle', 'API\ArticleController@getAticle');
    Route::get('deleteArticle/{id}', 'API\ArticleController@deleteArticle');
    Route::get('deleteExtraArticles/{id}', 'API\ArticleController@deleteExtraArticles');
    Route::get('connect/{id}', 'API\BaseController@connectServer');
    Route::get('increase/{id}', 'API\BaseController@increaseTime');
    Route::get('checkCountry/{id}', 'API\BaseController@checkCountry');
    Route::get('getRole/{id}', 'API\BaseController@getRole');
    Route::post('changeprofile', 'API\RegisterController@changeprofile');
    Route::get('refreshUser', 'API\RegisterController@refreshUser');
    Route::post('getOwnerArticleCount', 'API\ArticleController@getOwnerArticleCount');

    Route::get ('users/{id}', 'API\RegisterController@getUsers');

    //////////device token/////////
    Route::post('token', 'API\RegisterController@token');
    Route::post('token/remove', 'API\RegisterController@remove_token');
    // notification
    Route::get('coach/role', 'API\CallPlayerController@getRole');
    Route::post('coach/call', 'API\CallPlayerController@callPlayers');

    Route::get('notification', 'API\CallPlayerController@getNotifications');
    Route::get('notification/send', 'API\CallPlayerController@sendedNotifications');
    Route::post('notification', 'API\CallPlayerController@answerNotifications');
    Route::get('notification/delete/{id}', 'API\CallPlayerController@deleteNotification');

    Route::get('favour', 'API\BaseController@getFavour');
    Route::post('favour', 'API\BaseController@favour');
});
