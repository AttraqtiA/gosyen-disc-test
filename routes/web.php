<?php
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DiscTestController;
use App\Http\Controllers\DiscResultController;
use App\Http\Controllers\DiscHandbookController;
use App\Http\Controllers\MbtiTestController;
use App\Http\Controllers\MbtiResultController;
use App\Http\Controllers\OceanTestController;
use App\Http\Controllers\OceanResultController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\TestSessionController;
use App\Http\Controllers\Admin\PositionController;
use App\Http\Controllers\Admin\CustomTestController;
use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\ReviewController;

Route::get('/', [DiscTestController::class, 'codeEntry']);
Route::post('/access', [DiscTestController::class, 'accessByCode']);
Route::get('/start/{code}', [DiscTestController::class, 'start']);
Route::post('/start', [DiscTestController::class, 'storeMeta']);

Route::get('/test/{test}/question/{number}', [DiscTestController::class, 'question']);
Route::post('/test/{test}/answer', [DiscTestController::class, 'answer']);

Route::get('/test/{test}/result', [DiscResultController::class, 'show']);
Route::get('/handbook/disc', [DiscHandbookController::class, 'index']);
Route::get('/handbook', [DiscHandbookController::class, 'index']);

Route::get('/mbti/start/{code}', [MbtiTestController::class, 'start']);
Route::post('/mbti/start', [MbtiTestController::class, 'storeMeta']);
Route::get('/mbti/test/{test}/question/{number}', [MbtiTestController::class, 'question']);
Route::post('/mbti/test/{test}/answer', [MbtiTestController::class, 'answer']);
Route::get('/mbti/test/{test}/result', [MbtiResultController::class, 'show']);

Route::get('/ocean/start/{code}', [OceanTestController::class, 'start']);
Route::post('/ocean/start', [OceanTestController::class, 'storeMeta']);
Route::get('/ocean/test/{test}/question/{number}', [OceanTestController::class, 'question']);
Route::post('/ocean/test/{test}/answer', [OceanTestController::class, 'answer']);
Route::get('/ocean/test/{test}/result', [OceanResultController::class, 'show']);

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/admin', function () {
        $user = request()->user();

        if ($user?->role === 'reviewer') {
            return redirect('/admin/reviews');
        }

        return redirect('/admin/sessions');
    })->middleware('role:superadmin,client_admin,reviewer');

    Route::middleware('role:superadmin,client_admin')->group(function () {
        Route::get('/admin/sessions', [TestSessionController::class, 'index']);
        Route::post('/admin/sessions', [TestSessionController::class, 'store']);
        Route::patch('/admin/sessions/{session}', [TestSessionController::class, 'update']);
        Route::delete('/admin/sessions/{session}', [TestSessionController::class, 'destroy']);
        Route::patch('/admin/sessions/{session}/toggle', [TestSessionController::class, 'toggle']);

        Route::get('/admin/positions', [PositionController::class, 'index']);
        Route::post('/admin/positions', [PositionController::class, 'store']);
        Route::patch('/admin/positions/{position}/toggle', [PositionController::class, 'toggle']);
        Route::patch('/admin/positions/{position}/profile', [PositionController::class, 'updateProfile']);
        Route::post('/admin/positions/{position}/clients', [PositionController::class, 'attachClient']);
        Route::delete('/admin/positions/{position}/clients/{client}', [PositionController::class, 'detachClient']);

        Route::get('/admin/custom-tests', [CustomTestController::class, 'index']);
        Route::get('/admin/analytics', [AnalyticsController::class, 'index']);
        Route::get('/admin/exports/tests.csv', [AnalyticsController::class, 'exportAll']);
        Route::get('/admin/exports/sessions/{session}.csv', [AnalyticsController::class, 'exportSession']);
        Route::get('/admin/exports/disc/questions.csv', [AnalyticsController::class, 'exportDiscQuestions']);
        Route::get('/admin/exports/disc/manual.csv', [AnalyticsController::class, 'exportDiscManual']);
        Route::post('/admin/custom-tests', [CustomTestController::class, 'store']);
        Route::patch('/admin/custom-tests/{test}', [CustomTestController::class, 'update']);
        Route::delete('/admin/custom-tests/{test}', [CustomTestController::class, 'destroy']);
        Route::patch('/admin/custom-tests/{test}/toggle', [CustomTestController::class, 'toggle']);
        Route::get('/admin/custom-tests/{test}', [CustomTestController::class, 'show']);
        Route::post('/admin/custom-tests/{test}/dimensions', [CustomTestController::class, 'storeDimension']);
        Route::post('/admin/custom-tests/{test}/questions', [CustomTestController::class, 'storeQuestion']);
        Route::post('/admin/custom-tests/{test}/questions/{question}/options', [CustomTestController::class, 'storeOption']);
        Route::post('/admin/custom-tests/{test}/position-rules', [CustomTestController::class, 'upsertPositionRule']);
    });

    Route::middleware('role:superadmin,reviewer')->group(function () {
        Route::get('/admin/reviews', [ReviewController::class, 'index']);
        Route::patch('/admin/reviews/{answer}', [ReviewController::class, 'update']);
    });
});
