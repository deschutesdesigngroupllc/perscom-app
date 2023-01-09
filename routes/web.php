<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\RegisterController;
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
    Route::get('/complete', [RegisterController::class, 'complete'])->name('register.complete');
});
