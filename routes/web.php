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

Route::get('/', 'HomeController@index');
Route::get('/home', 'HomeController@dashboard')->name('home');

Route::get('/settings/profile', 'ProfileController@index')->name('profile.settings');
Route::post('/settings/profile', 'ProfileController@edit');
Route::post('/settings/password', 'ProfileController@changePassword')->name('profile.password');

Route::get('/settings/app', 'AppSettingsController@index')->name('app.settings');
Route::post('/settings/app', 'AppSettingsController@save');

Route::get('/timestamps/{year?}', 'TimestampController@index')->name('timestamp.months');
Route::get('/timestamps/{year}/month/{month}', 'TimestampController@month')->name('timestamp.month');
Route::get('/timestamps/day/{day}', 'TimestampController@day')->name('timestamp.day');

Route::post('/timestamps/insert', 'TimestampRegistryController@insert')->name('timestamp.insert');
Route::post('/timestamps/delete/{id}', 'TimestampRegistryController@delete')->name('timestamp.delete');

// Authentication Routes...
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
// Route::post('login', 'Auth\LoginController@login');
Route::post('logout', 'Auth\LoginController@logout')->name('logout');

//Spotify Auth...
Route::get('spotify_login', 'SpotifyController@authenticate')->name('spotify.login');
Route::get('spotify_callback', 'SpotifyController@callbackHandler')->name('spotify.callback');

// Registration Routes...
// Route::get('register', 'Auth\RegisterController@showRegistrationForm')->name('register');
// Route::post('register', 'Auth\RegisterController@register');

// Password Reset Routes...
// Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
// Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
// Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
// Route::post('password/reset', 'Auth\ResetPasswordController@reset');
