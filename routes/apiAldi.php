<?php

use App\Http\Controllers\Api\ApiAldiController;
use Illuminate\Support\Facades\Route;

Route::controller(ApiAldiController::class)
    ->name('apialdi.')
    ->group(function () {
        Route::get('/gudang_grading', 'gudang_grading')->name('gudang_grading');
        Route::post('/saveSuntikanGrading', 'saveSuntikanGrading')->name('saveSuntikanGrading');
        Route::post('/saveSuntikanSelesaiGrading', 'saveSuntikanSelesaiGrading')->name('saveSuntikanSelesaiGrading');
    });
