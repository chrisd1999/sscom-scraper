<?php

use App\Http\Controllers\AdvertisementAllController;
use App\Http\Controllers\AdvertisementController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
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
    Route::get('/ads/scrape', [AdvertisementController::class, 'store']);
    Route::get('/ads/scrape-all', [AdvertisementAllController::class, 'store'])->name('api.scrapeall');
    Route::get('/ads/{id}', [AdvertisementController::class, 'show']);
});
