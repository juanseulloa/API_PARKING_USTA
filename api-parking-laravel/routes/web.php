<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\ApiAuthMiddeleware;

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

Route::get('/', function () {
    return view('welcome');
});


/**
 * Routes Controller User
 */

Route::resource('/api/user', 'UserController')->middleware(ApiAuthMiddeleware::class);
Route::get('/api/users/{rol_user}', 'UserController@users')->middleware(ApiAuthMiddeleware::class);
Route::post('/api/user/create', 'UserController@create')->middleware(ApiAuthMiddeleware::class);
Route::get('/api/user/delete/{document}', 'UserController@delete')->middleware(ApiAuthMiddeleware::class);
Route::get('/api/user/get/{document}', 'UserController@getByDocument')->middleware(ApiAuthMiddeleware::class);
/**
 * Routes Controller Login

 */
Route::post('/api/login', 'LoginController@login');
Route::post('/api/login/create', 'LoginController@create');

/**
 * Routes Controller Rate
 */
Route::resource('/api/rate','RateController')->middleware(ApiAuthMiddeleware::class);
Route::post('/api/rate/create','RateController@create')->middleware(ApiAuthMiddeleware::class);

/**
 * Routes Controller
 *
 */

Route::resource('api/service','ServiceController')->middleware(ApiAuthMiddeleware::class);
Route::post('api/service/create','ServiceController@create')->middleware(ApiAuthMiddeleware::class);
Route::get('api/service/ticket/{document}','ServiceController@getTicket')->middleware(ApiAuthMiddeleware::class);
Route::get('api/service/report/{date}','ServiceController@reportByMonth')->middleware(ApiAuthMiddeleware::class);
Route::get('api/service/reports/{rate}','ServiceController@reportByRate')->middleware(ApiAuthMiddeleware::class);
Route::get('api/service/report/User','ServiceController@reportUser')->middleware(ApiAuthMiddeleware::class);

