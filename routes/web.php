<?php

use App\Http\Controllers\FindMyOrganizationController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PrivacyPolicyController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\Swagger\HomeController as SwaggerController;
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

Route::get('/', [HomeController::class, 'index'])->name('landing.home');

Route::group(['prefix' => 'register'], function () {
    Route::get('/', [RegisterController::class, 'index'])->name('register.index');
    Route::post('/', [RegisterController::class, 'store'])->name('register.store');
    Route::get('/complete/{id}', [RegisterController::class, 'complete'])->middleware('signed')->name('register.complete');
});

Route::group(['prefix' => 'find-my-organization'], function () {
    Route::get('/', [FindMyOrganizationController::class, 'index'])->name('find-my-organization.index');
    Route::post('/', [FindMyOrganizationController::class, 'store'])->name('find-my-organization.store');
    Route::get('/{tenant}', [FindMyOrganizationController::class, 'show'])->middleware('signed')->name('find-my-organization.show');
});

Route::get('/privacy-policy', [PrivacyPolicyController::class, 'index'])->name('privacy-policy.index');

Route::get('documentation/api', [SwaggerController::class, 'index']);
