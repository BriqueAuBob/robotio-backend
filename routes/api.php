<?php

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

// phpcs:disable Generic.Files.LineLength

// api.ro-bot.io/
Route::get("auth", "Api\AuthController@auth")->name("auth");
Route::post("auth/authorize", "Api\AuthController@authorizeUser")->name("auth.authorize");
Route::post("logout", "Api\AuthController@logout")->name("logout");

Route::prefix("v1")->namespace("Api\V1")->group(static function () {

    // ~/users
    Route::prefix("users")->group(static function () {

        // ~/@me
        Route::prefix("@me")->middleware(["auth:api"])->group(static function() {
            Route::get("/", "UserController@me")->name("me.get");
        });

    });

    // ~/applications
    Route::prefix("applications")->middleware(["auth:api"])->group(static function() {
        Route::get("/", "ApplicationController@index")->name("applications.index");
        Route::get("/{id}", "ApplicationController@get")->name("applications.get");
        Route::put("/{id}/synchronize", "ApplicationController@sync")->name("applications.sync");
        Route::post("/", "ApplicationController@store")->name("applications.store");
    });

});
    
