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

    Route::get("/paypal/redirect", "UserController@paypalRedirect")->name("me.paypal.redirect");
    Route::get("/paypal/validate", "UserController@paypalValidate")->name("me.paypal.validate");
    // ~/users
    Route::prefix("users")->group(static function () {

        // ~/@me
        Route::prefix("@me")->middleware(["auth:api"])->group(static function() {
            Route::get("/", "UserController@me")->name("me.get");
        });

    });

    // ~/applications
    Route::prefix("applications")->group(static function() {
        Route::get("/", "ApplicationController@index")->middleware(["auth:api"])->name("applications.index");
        Route::get("/{id}", "ApplicationController@get")->name("applications.get");
        Route::put("/{id}", "ApplicationController@edit")->middleware(["auth:api"])->name("applications.edit");
        Route::put("/{id}/synchronize", "ApplicationController@sync")->middleware(["auth:api"])->name("applications.sync");

        Route::get("/{id}/log", "LogController@index")->middleware(["auth:api"])->name("logs.index");
        Route::post("/{id}/log", "LogController@store")->name("logs.store");

        Route::get("/{id}/modules/{type}", "ModuleController@get")->name("modules.get");
        Route::put("/{id}/modules/{type}", "ModuleController@edit")->name("modules.edit");
        Route::post("/{id}/modules", "ModuleController@store")->middleware(["auth:api"])->name("modules.store");

        Route::post("/", "ApplicationController@store")->middleware(["auth:api"])->name("applications.store");
    });

});
    
