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

        # Horizons
        Route::prefix('horizons')->as('horizons.')->group(function () {
            Route::get('/', config('fair-queue.controllers.horizons') . '@index')->name('index');
            Route::get('scope', config('fair-queue.controllers.horizons') . '@scope')->name('scope');
            Route::post('store', config('fair-queue.controllers.horizons') . '@store')->name('store');
            Route::patch('{id?}', config('fair-queue.controllers.horizons') . '@update')->name('update');
            Route::delete('{id?}', config('fair-queue.controllers.horizons') . '@destroy')->name('destroy');
        });

        # Queues
        Route::prefix('queues')->as('queues.')->group(function () {
            Route::get('/', config('fair-queue.controllers.queues') . '@index')->name('index');
            Route::get('scope', config('fair-queue.controllers.queues') . '@scope')->name('scope');
            Route::post('store', config('fair-queue.controllers.queues') . '@store')->name('store');
            Route::patch('{id?}', config('fair-queue.controllers.queues') . '@update')->name('update');
            Route::delete('{id?}', config('fair-queue.controllers.queues') . '@destroy')->name('destroy');
            Route::get('model-collection', config('fair-queue.controllers.queues') . '@modelCollection')->name('model_collection');
        });

        # Supervisors
        Route::prefix('supervisors')->as('supervisors.')->group(function () {
            Route::get('/', config('fair-queue.controllers.supervisors') . '@index')->name('index');
            Route::get('scope', config('fair-queue.controllers.supervisors') . '@scope')->name('scope');
            Route::post('store', config('fair-queue.controllers.supervisors') . '@store')->name('store');
            Route::patch('{id?}', config('fair-queue.controllers.supervisors') . '@update')->name('update');
            Route::delete('{id?}', config('fair-queue.controllers.supervisors') . '@destroy')->name('destroy');
        });

        # Accesses
        Route::prefix('accesses')->as('accesses.')->group(function () {
            Route::get('/', config('fair-queue.controllers.accesses') . '@index')->name('index');
            Route::get('scope', config('fair-queue.controllers.accesses') . '@scope')->name('scope');
            Route::post('store', config('fair-queue.controllers.accesses') . '@store')->name('store');
            Route::patch('{id?}', config('fair-queue.controllers.accesses') . '@update')->name('update');
            Route::delete('{id?}', config('fair-queue.controllers.accesses') . '@destroy')->name('destroy');
            Route::get('objects', config('fair-queue.controllers.accesses') . '@objects')->name('objects');

        });
});




