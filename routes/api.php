<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('login', 'App\Http\Controllers\Api\UserController@login');
Route::post('registration', 'App\Http\Controllers\Api\UserController@store');

Route::middleware(['auth:api'])->group(function () {
    Route::get('/logout', 'App\Http\Controllers\Api\UserController@logout');

    Route::group(['prefix' => 'desks', 'middleware' => 'api'], function () {
        Route::get('/', 'App\Http\Controllers\Api\DeskController@index');
        Route::post('create', 'App\Http\Controllers\Api\DeskController@store');
        Route::patch('edit/{id}', 'App\Http\Controllers\Api\DeskController@update');
        Route::delete('delete/{id}', 'App\Http\Controllers\Api\DeskController@destroy');
    });

    Route::group(['prefix' => 'users', 'middleware' => 'api'], function () {
        Route::get('avatar', 'App\Http\Controllers\Api\UserController@avatar');
        Route::patch('edit', 'App\Http\Controllers\Api\UserController@update');
        Route::post('edit/avatar', 'App\Http\Controllers\Api\UserController@updateAvatar');
        Route::delete('delete', 'App\Http\Controllers\Api\UserController@destroy');
    });

    Route::group(['prefix' => 'tasks', 'middleware' => 'api'], function () {
        Route::get('/', 'App\Http\Controllers\Api\TaskController@index');
        Route::get('/uncompleted', 'App\Http\Controllers\Api\TaskController@uncompletedTasks');
        Route::get('/completed', 'App\Http\Controllers\Api\TaskController@completedTasks');
        Route::get('/important', 'App\Http\Controllers\Api\TaskController@importantTasks');
        Route::post('/important/{id}', 'App\Http\Controllers\Api\TaskController@taskImportant');
        Route::post('create', 'App\Http\Controllers\Api\TaskController@store');
        Route::patch('edit/{id}', 'App\Http\Controllers\Api\TaskController@update');
        Route::delete('delete/{id}', 'App\Http\Controllers\Api\TaskController@destroy');
        Route::post('complete/{id}', 'App\Http\Controllers\Api\TaskController@complete');
    });

    Route::group(['prefix' => 'cards', 'middleware' => 'api'], function () {
        Route::get('/', 'App\Http\Controllers\Api\CardController@index');
        Route::post('create', 'App\Http\Controllers\Api\CardController@store');
        Route::patch('edit/{id}', 'App\Http\Controllers\Api\CardController@update');
        Route::delete('delete/{id}', 'App\Http\Controllers\Api\CardController@destroy');
    });
});
