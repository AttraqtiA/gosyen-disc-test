<?php
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DiscTestController;
use App\Http\Controllers\DiscResultController;
use App\Http\Controllers\DiscHandbookController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\TestSessionController;
use App\Http\Controllers\Admin\PositionController;

Route::get('/', [DiscTestController::class, 'codeEntry']);
Route::post('/access', [DiscTestController::class, 'accessByCode']);
Route::get('/start/{code}', [DiscTestController::class, 'start']);
Route::post('/start', [DiscTestController::class, 'storeMeta']);

Route::get('/test/{test}/question/{number}', [DiscTestController::class, 'question']);
Route::post('/test/{test}/answer', [DiscTestController::class, 'answer']);

Route::get('/test/{test}/result', [DiscResultController::class, 'show']);
Route::get('/handbook/disc', [DiscHandbookController::class, 'index']);

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

    Route::get('/admin/positions', [PositionController::class, 'index']);
    Route::post('/admin/positions', [PositionController::class, 'store']);
    Route::patch('/admin/positions/{position}/toggle', [PositionController::class, 'toggle']);
    Route::patch('/admin/positions/{position}/profile', [PositionController::class, 'updateProfile']);
    Route::post('/admin/positions/{position}/clients', [PositionController::class, 'attachClient']);
    Route::delete('/admin/positions/{position}/clients/{client}', [PositionController::class, 'detachClient']);
});
