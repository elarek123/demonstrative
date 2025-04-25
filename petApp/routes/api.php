<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::controller(\App\Http\Controllers\GeoController::class)->group(function () {
    Route::get('/geos', 'index');
    Route::post('/geos', 'store');
    Route::get('/geos/{geo}', 'show');
    Route::put('/geos/{geo}', 'update');
    Route::delete('/geos/{geo}', 'delete');

});

Route::controller(\App\Http\Controllers\LeadController::class)->group(function () {
    Route::get('/leads', 'index');
    Route::post('/leads', 'store');
    Route::get('/leads/{lead}', 'show');
    Route::put('/leads/{lead}', 'update');
    Route::delete('/leads/{lead}', 'delete');
});

Route::controller(\App\Http\Controllers\ProductController::class)->group(function () {
    Route::get('/products', 'index');
    Route::post('/products', 'store');
    Route::get('/products/{product}', 'show');
    Route::put('/products/{product}', 'update');
    Route::delete('/products/{product}', 'delete');

});

Route::controller(\App\Http\Controllers\AuthController::class)->prefix('auth')->group(function () {
    Route::post('/signin', 'signin');
    Route::post('/signup', 'signup');
    Route::post('/telegram', 'telegramData');
    Route::post('/confirmation', 'userConfirmation');
});

Route::controller(\App\Http\Controllers\ProductGeoController::class)->group(function(){
    Route::get('/product-geos', 'getProductGeos');
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/product-geos/liked', 'getLikedProductGeos');
        Route::post('/product-geos/{product_geo}/like', 'like');
        Route::post('/product-geos/{product_geo}/unlike', 'unlike');
    });
});

