<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\IndexController;

Route::get('/{period?}', [IndexController::class, 'index'])->where("period", "3|7|30");
Route::get('/schedule/{dt}/{period}', [IndexController::class, 'schedule'])
        ->where("period", "3|7|30")
        ->where("dt", "\d{8}")->name('schedule');

Route::get('/reports', function () {
    return view('reports');
})->name("reports");

