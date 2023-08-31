<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Laporan_layerController;
use App\Http\Controllers\NavbarController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('login');
});

Route::get('/template1', function () {
    return view('template-notable');
})->name('template1');
Route::get('/template2', function () {
    return redirect()->route('dashboard.index');
})->name('template2');


Route::get('/dashboard', function () {
    return redirect()->route('template1');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {

    // 
    Route::controller(NavbarController::class)->group(function () {
        Route::get('/data_master', 'data_master')->name('data_master');
        Route::get('/home', 'home')->name('home');
    });
    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile', 'edit')->name('profile.edit');
        Route::patch('/profile', 'update')->name('profile.update');
        Route::delete('/profile', 'destroy')->name('profile.destroy');
    });

    Route::controller(DashboardController::class)
        ->prefix('dashboard')
        ->name('dashboard.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/detail/{nobox}', 'detail')->name('detail');
        });
});


Route::controller(Laporan_layerController::class)->group(function () {
    Route::get('/laporan_layer', 'index')->name('laporan_layer');
    Route::get('/rumus_layer', 'rumus_layer')->name('rumus_layer');
});
