<?php

use App\Http\Controllers\Api\ApiAldiController;
use App\Http\Controllers\ApiBkController;
use App\Http\Controllers\OpnameController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::controller(ApiAldiController::class)
    ->name('apialdi.')
    ->group(function () {
        Route::get('/gudang_grading', 'gudang_grading')->name('gudang_grading');
    });
