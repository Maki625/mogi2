<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\AttendanceController as UserAttendanceController;
use App\Http\Controllers\User\CorrectionRequestController as UserCorrectionRequestController;
use App\Http\Controllers\Admin\AttendanceController as AdminAttendanceController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('/', function () {
    if (auth()->check() && auth()->user()->admin_status) {
        return redirect('/admin/attendance/list');
    }

    if (auth()->check()) {
        return redirect('/attendance');
    }

    return redirect('/admin/login');
});

//管理者ユーザーログイン画面
Route::get('/admin/login', function () { return view('admin.login');
        })->name('admin.login');

//管理者勤怠一覧画面
Route::get('/admin/attendance/list', [AdminAttendanceController::class, 'index'])->middleware('admin');

//ユーザー出勤登録画面
Route::get('/attendance', [UserAttendanceController::class, 'index']);

// 出勤
Route::post('/attendance/start', [UserAttendanceController::class, 'start']);

// 休憩入り
Route::post('/attendance/break/start', [UserAttendanceController::class, 'breakStart']);

// 休憩終了
Route::post('/attendance/break/end', [UserAttendanceController::class, 'breakEnd']);

// 退勤
Route::post('/attendance/end', [UserAttendanceController::class, 'end']);

//勤怠一覧ページ
Route::get('/attendance/list', [UserAttendanceController::class, 'list']);

//勤怠詳細ページ
Route::get('/attendance/detail/{id}', [UserAttendanceController::class, 'show']);

//勤怠詳細修正処理
Route::post('/attendance/fix/{id}', [UserCorrectionRequestController::class, 'store']);

//申請一覧ページ
Route::get('/stamp_correction_request/list', [UserCorrectionRequestController::class, 'index']);
