<?php

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

// Route::group(['middlewareGroups' => ['activity', 'web']], static function () {

//     // ShortLink feature redirection
//

//     // Routes allowed as guest
//     Route::middleware('guest')->group(static function () {
//         Route::get('/', 'MainController@homepageWithoutLogin')
//                 ->name('homepage.without.login');

//         Route::get('auth/discord', 'Auth\AuthenticationController@redirectToProvider')
//                 ->name('login.thought.discord');

//         Route::get('auth/discord/callback', 'Auth\AuthenticationController@handleProviderCallback');
//     });

//     Route::prefix('legal')->group(static function () {
//         Route::get('/terms-of-service', 'LegalController@terms')->name('terms.of.service');
//         Route::get('/disclaimer', 'LegalController@disclaimer')->name('disclaimer');
//     });

//     // Routes allowed if user is auth and if user is michel
//     Route::middleware(['auth', 'is_michel'])->group(static function () {

//         Route::prefix('legal')->group(static function () {
//                 Route::get('/acceptation', 'LegalController@acceptation')->name('terms.acceptation');
//                 Route::get('/accept-terms', 'LegalController@acceptTerms')->name('accept.terms');
//                 Route::get('/decline-terms', 'LegalController@declineTerms')->name('decline.terms');
//         });

//         Route::middleware(['accept_terms'])->group(function () {

//                 Route::get('/home', 'MainController@homepage')
//                         ->name('homepage');
//                 Route::get('/partners', 'MainController@partners')
//                         ->name('partners');
//                 Route::get('/roadmap', 'MainController@roadmap')
//                         ->name('roadmap');
//                 Route::get('auth/logout', 'Auth\AuthenticationController@logout')
//                         ->name('logout');

//                 // ~ /profile/
//                 Route::prefix('profile')->group(static function () {
//                         Route::get('/{user}', 'ProfileController@view')->name('profile');
//                 });

//                 // ~ /creators-battle
//                 Route::prefix('creators-battle')->group(static function () {
//                 Route::get('/', 'CreatorsBattleController@index')
//                         ->name('creator.battle');
//                 Route::post('/register', 'CreatorsBattleController@registerTeam')
//                         ->middleware(['has_react'])
//                         ->name('creator.battle.register.team');
//                 Route::post('/resource/change', 'CreatorsBattleController@registerResource')
//                         ->name('creator.battle.register.mtx_resource');
//                 });

//                 // Notifications handler
//                 Route::get('read/all', 'NotificationController@readAll')
//                         ->name('read.all.notifications');
//                 Route::get('read/{notification}', 'NotificationController@show')
//                         ->name('read.notification');

//                 Route::prefix('groups')->as('groups.')->middleware(['has_react'])->group(static function () {
//                 Route::get('/{group}/requests/{request}/accept', 'GroupController@acceptRequest')
//                         ->name('accept.request');
//                 Route::get('/{group}/requests/{request}/decline', 'GroupController@declineRequest')
//                         ->name('decline.request');
//                 });

//                 // Dashboard ~ /_admin
//                 Route::prefix('_admin')->as('admin.')->middleware(['permission:access-dashboard'])->group(static function () {
//                 Route::get('/', 'Dashboard\HomeController@index')
//                         ->name('dashboard.index');

//                 Route::prefix('users')->group(static function () {
//                         Route::get('/', 'Dashboard\UserController@index')
//                         ->middleware(['permission:read-users'])
//                         ->name('dashboard.users');

//                         Route::match(['get', 'patch'], '/{user}/', 'Dashboard\UserController@update')
//                         ->middleware(['permission:update-users'])
//                         ->name('dashboard.users.update');
//                 });

//                 // Handle roles ~ /_admin/roles
//                 Route::prefix('roles')->group(static function () {
//                         Route::get('/', 'Dashboard\RoleController@index')
//                                 ->middleware(['permission:read-roles'])
//                                 ->name('dashboard.roles');

//                         Route::delete('/{role}/', 'Dashboard\RoleController@delete')
//                                 ->middleware(['permission:delete-roles'])
//                                 ->name('dashboard.roles.delete');

//                         Route::match(['get', 'patch'], '/{role}/', 'Dashboard\RoleController@update')
//                                 ->middleware(['permission:update-roles'])
//                                 ->name('dashboard.roles.update');
//                 });

//                 // Handle shortlinks ~ /_admin/shortlinks
//                 Route::prefix('shortlinks')->group(static function () {
//                         Route::get('/', 'Dashboard\ShortLinkController@index')
//                                 ->middleware(['permission:read-shortlink'])
//                                 ->name('dashboard.shortlinks');

//                         Route::match(['get', 'post'], '/store', 'Dashboard\ShortLinkController@store')
//                                 ->middleware(['permission:create-shortlink'])
//                                 ->name('dashboard.shortlinks.store');

//                         Route::delete('/{shortlink}', 'Dashboard\ShortLinkController@delete')
//                                 ->middleware(['permission:delete-shortlink'])
//                                 ->name('dashboard.shortlinks.delete');
//                 });

//                 // Handle partners ~ /_admin/partners
//                 Route::prefix('partners')->group(static function () {
//                         Route::get('/', 'Dashboard\PartnerController@index')
//                                 ->middleware(['permission:read-partners'])
//                                 ->name('dashboard.partners');

//                         Route::get('/create', 'Dashboard\PartnerController@create')
//                                 ->middleware(['permission:create-partners'])
//                                 ->name('dashboard.partners.create');

//                         Route::match(['get', 'patch'], '/{partner}', 'Dashboard\PartnerController@update')
//                                 ->middleware(['permission:update-partners'])
//                                 ->name('dashboard.partners.update');

//                         Route::delete('/{partner}', 'Dashboard\PartnerController@delete')
//                                 ->middleware(['permission:delete-partners'])
//                                 ->name('dashboard.partners.delete');

//                         Route::post('', 'Dashboard\PartnerController@store')
//                                 ->middleware(['permission:create-partners'])
//                                 ->name('dashboard.partners.store');
//                 });

//                 Route::match(['get', 'patch'], '/creators-battle', 'Dashboard\HomeController@creatorsBattle')
//                         ->name('dashboard.creators.battle');
//                 Route::get('groups/{group}/delete', 'Dashboard\HomeController@deleteGroup')
//                         ->name('dashboard.delete.group');
//                 Route::get('groups/{group}/remove/{user}', 'Dashboard\HomeController@removeUserFromGroup')
//                         ->name('dashboard.delete.member.from.group');
//                 });
//         });

//     });
// });
