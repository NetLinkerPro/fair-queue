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
Route::domain(config('fair-queue.domain'))
    ->name('fair-queue.')
    ->prefix(config('fair-queue.prefix'))
    ->middleware(config('fair-queue.middleware'))
    ->group(function () {

        # Assets AWES
        Route::get('assets/{module}/{type}/{filename}', config('fair-queue.controllers.assets') . '@getAwes')->name('assets.awes');

        # Dashboard
        Route::prefix('/')->as('dashboard.')->group(function () {
            Route::get('/', config('fair-queue.controllers.dashboard') . '@index')->name('index');
        });

        # Jobs
        Route::prefix('job-statuses')->as('job_statuses.')->group(function () {
            Route::get('/', config('fair-queue.controllers.job_statuses') . '@index')->name('index');
            Route::get('scope', config('fair-queue.controllers.job_statuses') . '@scope')->name('scope');
            Route::post('interrupt', config('fair-queue.controllers.job_statuses') . '@interrupt')->name('interrupt');
        });
});




