<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SearchController;
use App\Services\QueryBuilder;

Route::get('/', [SearchController::class, 'index']);
Route::post('/', [SearchController::class, 'search'])->name('search.submit');