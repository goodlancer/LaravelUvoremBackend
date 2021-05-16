<?php

use App\Http\Controllers\UserController;
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

Route::redirect('/', '/login');
Auth::routes();

Route::group(['middleware' => 'auth'], function () {
	Route::resource('dashboard', 'HomeController', ['except' => ['show']]);

	Route::resource('user', 'UserController', ['except' => ['show']]);
	Route::resource('admin', 'AdminController', ['except' => ['show']]);
	Route::resource('country', 'CountryController', ['except' => ['show']]);
	Route::resource('offer', 'OfferController', ['except' => ['show']]);
	Route::post('offer/edit', 'OfferController@edit');
	Route::post('users/positions', ['uses' => 'UserController@positions']);
	Route::get('user/{user}/{type}', ['as' => 'user.edit', 'uses' => 'UserController@edit']);
	Route::post('user/offer_publish', 'UserController@offerPublish');
	Route::post('user/country_state', 'UserController@countryState');
	Route::post('user/userActivate', 'UserController@userActivate');
	Route::delete('user/article/{id}', ['as' => 'article.delete', 'uses' => 'UserController@articleDelete']);

	// Route::get('user/article/filter/{userid}/{type}', ['as' => 'article.filter', 'uses' => 'UserController@articleFilter']);

	Route::post('user/getAllOffers', 'UserController@getAllOffers');
	Route::get('getUsers/{country}', 'UserController@getUsers');
	// notification
	Route::resource('notification', 'NotificationHistoryController', ['except' => ['show']]);
	Route::resource('offer_history', 'OfferHistoryController', ['except' => ['show']]);
	Route::get('matches/notification', ['uses' => 'MatchesController@getNotification']);
	Route::post('matches/notification', ['uses' => 'MatchesController@saveNotification']);

	Route::get('profile/{user}', ['as' => 'profile.edit', 'uses' => 'ProfileController@edit']);
	Route::put('profile/{user}', ['as' => 'profile.update', 'uses' => 'ProfileController@update']);
	Route::put('profile/password/{user}', ['as' => 'profile.password', 'uses' => 'ProfileController@password']);
});
