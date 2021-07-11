<?php

use App\Http\Controllers\AdvertisementController;
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

Route::group(['prefix' => '/v2'], function () {
    Route::get('/ads', [AdvertisementController::class, 'index']);
    Route::get('/scrape', [AdvertisementController::class, 'store']);
    Route::get('/ads/{id}', [AdvertisementController::class, 'show']);
});
