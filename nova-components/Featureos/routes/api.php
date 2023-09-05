<?php

use App\Services\FeatureOsService;
use Illuminate\Support\Facades\Route;

Route::get('login', function () {
    return response()->json([
        'jwt' => FeatureOsService::generateJwt(),
    ]);
});
