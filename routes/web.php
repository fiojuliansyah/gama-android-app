<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\ValetController;
use App\Http\Controllers\MinuteController;
use App\Http\Controllers\PermitController;
use App\Http\Controllers\PatrollController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReliverController;
use App\Http\Controllers\OvertimeController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\FindingsReportController;
use App\Http\Controllers\FaceRecognitionController;

Route::get('/download', [HomeController::class, 'download'])->name('download');

Route::prefix('mobile')->group(function() {
    Route::get('/get-started', [HomeController::class, 'getStarted'])->name('walkthrough');
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login'])->name('login');
    Route::post('logout', [LoginController::class, 'logout'])->middleware('web')->name('logout');
});

Route::middleware('auth')->prefix('mobile')->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/setting', [HomeController::class, 'setting'])->name('setting');
    Route::get('/schedule', [ScheduleController::class, 'index'])->name('schedule');
    Route::get('/schedule/{id}/show', [ScheduleController::class, 'show'])->name('schedule.show');
    Route::post('/schedule/progress/start', [ScheduleController::class, 'progressStart'])->name('progress.start');
    Route::post('/schedule/progress/end', [ScheduleController::class, 'progressStart'])->name('progress.end');

    Route::get('/face-register', [ProfileController::class, 'faceRegister'])->name('face.register');
    Route::post('/face/process', [FaceRecognitionController::class, 'processFace'])->name('face.process');

    Route::get('/account', [ProfileController::class, 'account'])->name('account');
    Route::post('/account/update', [ProfileController::class, 'updateAccount'])->name('update.account');

    Route::get('/profile', [ProfileController::class, 'profile'])->name('profile');
    Route::post('/profile/update', [ProfileController::class, 'updateProfile'])->name('update.profile');

    Route::get('/bank', [ProfileController::class, 'bank'])->name('bank');
    Route::post('/bank/update', [ProfileController::class, 'updateBank'])->name('update.bank');

    Route::get('/esign', [ProfileController::class, 'esign'])->name('esign');
    Route::post('/esign/update', [ProfileController::class, 'updateEsign'])->name('update.esign');

    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::get('/attendance/logs', [HomeController::class, 'logs'])->name('attendance.logs');
    Route::get('/attendance/clockin', [AttendanceController::class, 'clockinPage'])->name('attendance.clockin');
    Route::post('/attendance/clockin', [AttendanceController::class, 'clockinStore'])->name('clockin.store');
    Route::get('/attendance/clockout', [AttendanceController::class, 'clockoutPage'])->name('attendance.clockout');
    Route::post('/attendance/clockout', [AttendanceController::class, 'clockoutStore'])->name('clockout.store');
    Route::post('/attendance/off', [AttendanceController::class, 'timeOff'])->name('attendance.off');

    Route::get('/overtime', [OvertimeController::class, 'index'])->name('overtime.index');
    Route::post('/overtime/clockin', [OvertimeController::class, 'clockinStore'])->name('overtime.clockin');
    Route::post('/overtime/clockout', [OvertimeController::class, 'clockoutStore'])->name('overtime.clockout');

    Route::get('/minute', [MinuteController::class, 'index'])->name('minute.index');
    Route::get('/minute/create', [MinuteController::class, 'create'])->name('minute.create');
    Route::post('/minute/store', [MinuteController::class, 'minute'])->name('minute.store');
    Route::get('/minute/{id}/show', [MinuteController::class, 'show'])->name('minute.show');
    Route::get('/minute', [MinuteController::class, 'index'])->name('minute.index');

    Route::get('/findings-reports', [FindingsReportController::class, 'index'])->name('findings-reports.index');
    Route::get('/findings-reports/create', [FindingsReportController::class, 'create'])->name('findings-reports.create');
    Route::post('/findings-reports/store', [FindingsReportController::class, 'store'])->name('findings-reports.store');
    Route::get('/findings-reports/{id}/show', [FindingsReportController::class, 'show'])->name('findings-reports.show');

    Route::get('/leave', [LeaveController::class, 'index'])->name('leave.index');
    Route::get('/leave/create/{slug}', [LeaveController::class, 'createLeave'])->name('leave.create.main');
    Route::get('/leave/create', [LeaveController::class, 'create'])->name('leave.create');
    Route::post('/leave/store', [LeaveController::class, 'store'])->name('leave.store');
    Route::get('/leave/{id}/show', [LeaveController::class, 'show'])->name('leave.show');
    Route::put('/leave/{id}/update', [LeaveController::class, 'update'])->name('leave.update');

    Route::get('/permit', [PermitController::class, 'index'])->name('permit.index');
    Route::get('/permit/create', [PermitController::class, 'create'])->name('permit.create');
    Route::post('/permit/store', [PermitController::class, 'store'])->name('permit.store');
    Route::get('/permit/{id}/show', [PermitController::class, 'show'])->name('permit.show');
    Route::put('/permit/{id}/update', [PermitController::class, 'update'])->name('permit.update');

    Route::get('/reliver', [ReliverController::class, 'index'])->name('reliver.index');
    Route::post('/reliver/update-site', [ReliverController::class, 'updateSite'])->name('reliver.updateSite');
    Route::post('/reliver/clockin/site', [ReliverController::class, 'clockin'])->name('reliver.clockin');
    Route::post('/reliver/clockin', [ReliverController::class, 'clockinStore'])->name('reliver.clockin.store');
    Route::get('/reliver/clockout', [ReliverController::class, 'clockout'])->name('reliver.clockout');
    Route::post('/reliver/clockout', [ReliverController::class, 'clockoutStore'])->name('reliver.clockout.store');

    Route::get('/payslip', [HomeController::class, 'payslip'])->name('payslip');

    Route::get('/patroll', [PatrollController::class, 'index'])->name('patroll.index');

    Route::get('/patroll/scan', [PatrollController::class, 'scan'])->name('patroll.scan');

    Route::post('/patroll/start', [PatrollController::class, 'startSession'])->name('patroll.start');

    Route::get('/patroll/end-session/{session}', [PatrollController::class, 'endSession'])
        ->name('patroll.end-session');

    Route::get('/floor/{id}/patroll', [PatrollController::class, 'detailFloor'])->name('patroll.floor.detail');
    Route::post('/task-progress/{task}/update', [PatrollController::class, 'taskUpdate'])->name('patroll.task-progress.update');

});