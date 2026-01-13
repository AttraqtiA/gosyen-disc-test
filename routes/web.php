<?php
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DiscTestController;
use App\Http\Controllers\DiscResultController;

Route::get('/', [DiscTestController::class, 'start']);
Route::post('/start', [DiscTestController::class, 'storeMeta']);

Route::get('/test/{test}/question/{number}', [DiscTestController::class, 'question']);
Route::post('/test/{test}/answer', [DiscTestController::class, 'answer']);

Route::get('/test/{test}/result', [DiscResultController::class, 'show']);

