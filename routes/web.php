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
Route::get('/history', 'HomeController@history')->name('history');
Route::get('/clearNotifications', 'HomeController@clearNotifications')->name('notifications.clear');

Route::get('/preferences', 'PreferencesController@index')->name('preferences');
Route::post('/preferences', 'PreferencesController@edit');

Route::get('/widget', 'WidgetController@index')->name('widget');
Route::post('/widget', 'WidgetController@edit');

Route::get('/config', 'ConfigController@index')->name('config');
Route::post('/config', 'ConfigController@edit');

//Spotify Integration...
Route::get('/spotify/login', 'SpotifyController@authenticate')->name('spotify.login');
Route::get('/spotify/callback', 'SpotifyController@callbackHandler')->name('spotify.callback');
Route::get('/spotify/disconnect', 'SpotifyController@disconnect')->name('spotify.disconnect');

//Nightbot Integration...
Route::get('/nightbot/login', 'NightbotController@authenticate')->name('nightbot.login');
Route::get('/nightbot/callback', 'NightbotController@callbackHandler')->name('nightbot.callback');
Route::get('/nightbot/disconnect', 'NightbotController@disconnect')->name('nightbot.disconnect');
Route::get('/nightbot/test', 'NightbotController@sendTestMessage')->name('nightbot.test');

// Authentication Routes...
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
// Route::post('login', 'Auth\LoginController@login');
Route::post('logout', 'Auth\LoginController@logout')->name('logout');

// Registration Routes...
// Route::get('register', 'Auth\RegisterController@showRegistrationForm')->name('register');
// Route::post('register', 'Auth\RegisterController@register');

// Password Reset Routes...
// Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
// Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
// Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
// Route::post('password/reset', 'Auth\ResetPasswordController@reset');
