<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\AttendanceController as UserAttendanceController;
use App\Http\Controllers\User\CorrectionRequestController as UserCorrectionRequestController;
use App\Http\Controllers\Admin\AttendanceController as AdminAttendanceController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\CorrectionRequestController as AdminCorrectionRequestController;



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

//ログイン管理
Route::get('/', function () {
    return redirect('/login');
});

Route::get('/admin/login', function () {
    return view('admin.login');
})->name('admin.login');

Route::middleware('admin')->group(function () {
    Route::get('/admin/attendance/list', [AdminAttendanceController::class, 'index']);
});

//一般ユーザーホーム
Route::get('/attendance', [UserAttendanceController::class, 'index'])
    ->middleware('auth', 'verified')
    ->name('user.attendance.index');

// 出勤
Route::post('/attendance/start', [UserAttendanceController::class, 'start']);

// 休憩入り
Route::post('/attendance/break/start', [UserAttendanceController::class, 'breakStart']);

// 休憩終了
Route::post('/attendance/break/end', [UserAttendanceController::class, 'breakEnd']);

// 退勤
Route::post('/attendance/end', [UserAttendanceController::class, 'end']);

//勤怠レポートページ
Route::get('/attendance/report', [UserAttendanceController::class, 'report']);

//勤怠一覧ページ
Route::get('/attendance/list', [UserAttendanceController::class, 'list']);

//勤怠詳細ページ
Route::get('/attendance/detail/{id}', [UserAttendanceController::class, 'show']);

//勤怠詳細修正処理
Route::post('/attendance/fix/{id}', [UserCorrectionRequestController::class, 'store']);

//申請一覧ページ
Route::get('/stamp_correction_request/list', [UserCorrectionRequestController::class, 'index']);


//管理者ホーム
Route::get('/admin/attendance/list', [AdminAttendanceController::class, 'index'])
    ->name('admin.attendance.index');

//スタッフ一覧ページ
Route::get('/admin/staff/list', [AdminUserController::class, 'index']);

//スタッフ別勤怠一覧ページ
Route::get('/admin/attendance/list/{id}', [AdminAttendanceController::class, 'list']);

// スタッフ別勤怠CSV出力
Route::get('/admin/attendance/list/{id}/csv', [AdminAttendanceController::class, 'csv']);

//スタッフ別勤怠詳細ページ
Route::get('/admin/attendance/{user_id}/{date}', [AdminAttendanceController::class, 'show']);

//スタッフ別勤怠詳細修正処理
Route::put('/admin/attendance/fix/{user_id}/{date}', [AdminAttendanceController::class, 'update']);

//管理者申請一覧ページ
Route::get('/admin/stamp_correction_request/list', [AdminCorrectionRequestController::class, 'index']);

//申請詳細ページ
Route::get('/stamp_correction_request/approve/{attendance_correct_request_id}', [AdminCorrectionRequestController::class, 'show']);

//修正承認処理
Route::post('/admin/correction/{id}/approve', [AdminCorrectionRequestController::class, 'approve']
);
