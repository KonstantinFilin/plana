<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('index');
});

Route::get('/reports', function () {
    return view('reports');
})->name("reports");
