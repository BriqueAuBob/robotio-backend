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
        Route::get("/{id}", "ApplicationController@get")->middleware(["application:admin,modo"])->name("applications.get");
        Route::post("/", "ApplicationController@store")->middleware(["auth:api"])->name("applications.store");
        Route::put("/{id}", "ApplicationController@update")->middleware(["auth:api", "application"])->name("applications.edit");

        Route::put("/{id}/synchronize", "ApplicationController@sync")->middleware(["auth:api"])->name("applications.sync");

        Route::get("/{id}/log", "LogController@index")->middleware(["application"])->name("logs.index");
        Route::post("/{id}/log", "LogController@store")->middleware(["application"])->name("logs.store");

        // ~/{id}/modules/
        Route::group(["prefix" => "{id}/modules"], function () {
            Route::get("/", "ModuleController@index")->middleware(["application"])->name("modules.index");
            Route::get("/{type}", "ModuleController@get")->middleware(["application"])->name("modules.get");
            Route::put("/{type}", "ModuleController@update")->middleware(["application"])->name("modules.edit");
            Route::post("/", "ModuleController@store")->middleware(["application"])->name("modules.store");
        });

        // ~/{id}/
        Route::group(["prefix" => "{id}", "namespace" => "Modules"], function () {
            Route::get("warns/", "WarnController@index")->middleware(["application"])->name("warns.index");
            Route::get("warns/{warn}", "WarnController@get")->middleware(["application"])->name("warns.get");
            Route::post("warns/", "WarnController@store")->middleware(["application"])->name("warns.store");
            Route::delete("warns/", "WarnController@destroy")->middleware(["application"])->name("warns.get");

            Route::post("annoucement/", "AnnouncementController@send")->middleware(["application"])->name("annoucement.send");

            Route::get("minigames/score", "MinigameController@index")->middleware(["application"])->name("modules.minigames.index");
            Route::post("minigames/score", "MinigameController@store")->middleware(["application"])->name("modules.minigames.store");
        });
    });

});