<?php

use App\Http\Controllers\Dashboard\AuthController;
use App\Http\Controllers\Dashboard\ContentMonitorController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Dashboard\ExamController;
use App\Http\Controllers\Dashboard\PastExamController;
use App\Http\Controllers\Dashboard\PunishmentController;
use App\Http\Controllers\Dashboard\ReportController;
use App\Http\Controllers\Dashboard\RoomWebController;
use App\Http\Controllers\MagicLoginController;
use App\Http\Middleware\CheckAdminRole;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dashboard\SupportTicketController;
use App\Http\Controllers\NotificationController;

/*
'Super Admin',
'Academic Manager',
'Moderator',
'Support Agent',
'Data Entry',
'Financial Manager',
*/

Route::get('/', function () {
    return view('auth.login');
})->name('login');

Route::post('/login-form', [AuthController::class, 'login'])->name('dashboard.login');

Route::get('/magic-login/{id}', [MagicLoginController::class, 'showView'])
    ->name('student.magic.view')
    ->middleware('signed');

Route::post('/magic-login/generate/{id}', [MagicLoginController::class, 'generateToken'])
    ->name('student.magic.generate')
    ->middleware('signed');

Route::middleware([CheckAdminRole::class . ':Super Admin,Academic Manager,Moderator,Support Agent,Data Entry,Financial Manager'])->group(function () {
    Route::get('/home', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/charts', function () {
        return view('dashboard');
    })->name('charts');
    Route::get('/classes', function () {
        return view('dashboard');
    })->name('classes');
});

Route::middleware([CheckAdminRole::class . ':Super Admin,Academic Manager'])->group(function () {
    Route::get('/active-calls', [RoomWebController::class, 'activeCalls'])->name('admin.active-calls');
    Route::get('/rooms', [RoomWebController::class, 'index'])->name('rooms.index');
    Route::get('/rooms/create', [RoomWebController::class, 'create'])->name('rooms.create');
    Route::post('/rooms', [RoomWebController::class, 'store'])->name('rooms.store');
    Route::get('/classes/{class_id}/rooms', [RoomWebController::class, 'classRooms'])->name('rooms.class');
    Route::get('/rooms/{room_name}/join', [RoomWebController::class, 'joincall'])->name('rooms.join');
    Route::prefix('rooms/actions')->name('rooms.actions.')->group(function () {
        Route::post('/kick', [RoomWebController::class, 'kickParticipant'])->name('kick');
        Route::post('/mute', [RoomWebController::class, 'muteParticipant'])->name('mute');
        Route::post('/unmute', [RoomWebController::class, 'unmuteParticipant'])->name('unmute');
        Route::post('/end', [RoomWebController::class, 'endCall'])->name('end');
    });
});

Route::middleware([CheckAdminRole::class . ':Super Admin,Moderator'])->group(function () {
    Route::get('/content-monitor', [ContentMonitorController::class, 'index'])->name('content.monitor');

    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/{id}', [ReportController::class, 'show'])->name('show');
        Route::post('/{id}/status', [ReportController::class, 'updateStatus'])->name('update-status');
    });
    Route::get('/logs', [DashboardController::class, 'logs'])->name('logs.index');
});

Route::middleware([CheckAdminRole::class . ':Super Admin,Moderator,Academic Manager'])->prefix('punishments')->name('punishments.')->group(function () {
    Route::get('/types', [PunishmentController::class, 'indexTypes'])->name('types.index');
    Route::post('/types', [PunishmentController::class, 'storeType'])->name('types.store');

    Route::get('/active', [PunishmentController::class, 'activePunishments'])->name('active');

    Route::post('/apply', [PunishmentController::class, 'apply'])->name('apply');
    Route::post('/{id}/revoke', [PunishmentController::class, 'revoke'])->name('revoke');
});


Route::middleware([CheckAdminRole::class . ':Super Admin,Academic Manager,Data Entry'])->group(function () {
    Route::get('/dashboard/students', [DashboardController::class, 'studentsList'])->name('students.index');
    Route::get('/dashboard/teachers', [DashboardController::class, 'teachersList'])->name('teachers.index');

    Route::get('/requests', function () {
        return view('dashboard');
    })->name('requests');
    Route::get('/requests/{type}', [DashboardController::class, 'showRequests'])->name('requests.view');
    Route::get('/request-details/{type}/{id}', [DashboardController::class, 'getRequestDetails']);
    Route::post('/request-update-status/{type}/{id}', [DashboardController::class, 'updateStatus'])->name('requests.update');

    Route::get('/dashboard/teachers/{id}/complete-approval', [DashboardController::class, 'showTeacherApprovalForm'])->name('teachers.approve.form');
    Route::post('/dashboard/teachers/{id}/complete-approval', [DashboardController::class, 'completeTeacherApproval'])->name('teachers.approve.submit');
});

Route::middleware([CheckAdminRole::class . ':Super Admin,Academic Manager,Data Entry'])->prefix('dashboard')->name('dashboard.')->group(function () {
    Route::post('past-exams/{id}/publish', [PastExamController::class, 'publish'])->name('past-exams.publish');
    Route::post('past-exams/{id}/questions', [PastExamController::class, 'storeQuestion'])->name('past-exams.questions.store');
    Route::post('past-exams/{id}/questions/attach', [PastExamController::class, 'attachQuestion'])->name('past-exams.questions.attach');
    Route::delete('past-exams/{id}/questions/{question_id}', [PastExamController::class, 'detachQuestion'])->name('past-exams.questions.detach');
    Route::resource('past-exams', PastExamController::class)->parameters(['past-exams' => 'id']);

    Route::post('exams/{id}/publish', [ExamController::class, 'publish'])->name('exams.publish');
    Route::post('exams/{id}/questions', [ExamController::class, 'storeQuestion'])->name('exams.questions.store');
    Route::post('exams/{id}/questions/attach', [ExamController::class, 'attachQuestion'])->name('exams.questions.attach');
    Route::delete('exams/{id}/questions/{question_id}', [ExamController::class, 'detachQuestion'])->name('exams.questions.detach');
    Route::get('exams/{id}/submissions', [ExamController::class, 'submissions'])->name('exams.submissions');
    Route::post('exams/{id}/approve-all', [ExamController::class, 'approveAllSubmissions'])->name('exams.approve-all');
    Route::post('exam-submissions/{submission_id}/approve', [ExamController::class, 'approveSubmission'])->name('exams.submissions.approve');
    Route::resource('exams', ExamController::class)->parameters(['exams' => 'id']);

});

Route::prefix('support')->name('dashboard.support.')->group(function () {
    Route::get('/', [SupportTicketController::class, 'index'])->name('index');
    Route::post('/{id}/assign', [SupportTicketController::class, 'assign'])->name('assign');
    Route::post('/{id}/status', [SupportTicketController::class, 'updateStatus'])->name('update-status');
});
Route::get('/send-test-notification', [NotificationController::class, 'testSend']);