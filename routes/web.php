<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SearchController;

Route::get('/search', [SearchController::class, 'index'])->name('search.form');
Route::post('/search', [SearchController::class, 'search'])->name('search.submit');

Route::get('/', function () {
    return redirect()->route('search.form');
});
