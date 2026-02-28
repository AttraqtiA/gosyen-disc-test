<?php
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DiscTestController;
use App\Http\Controllers\DiscResultController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\TestSessionController;

Route::get('/', [DiscTestController::class, 'codeEntry']);
Route::post('/access', [DiscTestController::class, 'accessByCode']);
Route::get('/start/{code}', [DiscTestController::class, 'start']);
Route::post('/start', [DiscTestController::class, 'storeMeta']);

Route::get('/test/{test}/question/{number}', [DiscTestController::class, 'question']);
Route::post('/test/{test}/answer', [DiscTestController::class, 'answer']);

Route::get('/test/{test}/result', [DiscResultController::class, 'show']);

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/admin', fn () => redirect('/admin/sessions'));
    Route::get('/admin/sessions', [TestSessionController::class, 'index']);
    Route::post('/admin/sessions', [TestSessionController::class, 'store']);
    Route::patch('/admin/sessions/{session}/toggle', [TestSessionController::class, 'toggle']);
});
