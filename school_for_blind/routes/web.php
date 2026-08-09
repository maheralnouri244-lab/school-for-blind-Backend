<?php

use App\Http\Controllers\Dashboard\AuthController;
use App\Http\Controllers\Dashboard\ContentMonitorController;
use App\Http\Controllers\Dashboard\ConversationWebController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Dashboard\ExamController;
use App\Http\Controllers\Dashboard\PastExamController;
use App\Http\Controllers\Dashboard\PunishmentController;
use App\Http\Controllers\Dashboard\ReportController;
use App\Http\Controllers\Dashboard\RoomWebController;
use App\Http\Controllers\Dashboard\SupportTicketController;
use App\Http\Controllers\MagicLoginController;
use App\Http\Controllers\NotificationController;
use App\Http\Middleware\CheckAdminRole;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dashboard\UserManagerController;
use App\Http\Controllers\Dashboard\ScheduleController;
use App\Http\Controllers\Dashboard\ClassController;
use App\Http\Controllers\Dashboard\StudentReportWebController;



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
    Route::get('/rooms/history', [RoomWebController::class, 'history'])->name('rooms.history');
    Route::get('/rooms/create', [RoomWebController::class, 'create'])->name('rooms.create');
    Route::post('/rooms', [RoomWebController::class, 'store'])->name('rooms.store');
    Route::get('/classes/{class_id}/rooms', [RoomWebController::class, 'classRooms'])->name('rooms.class');
    Route::get('/rooms/{room_name}/join', [RoomWebController::class, 'joincall'])->name('rooms.join');
    Route::post('/rooms/assign-subject', [RoomWebController::class, 'assignSubject'])->name('rooms.assign_subject');
    Route::post('/rooms/{id}/toggle-payment', [RoomWebController::class, 'togglePayment'])->name('rooms.toggle_payment');
    Route::prefix('rooms/actions')->name('rooms.actions.')->group(function () {
        Route::post('/kick', [RoomWebController::class, 'kickParticipant'])->name('kick');
        Route::post('/mute', [RoomWebController::class, 'muteParticipant'])->name('mute');
        Route::post('/unmute', [RoomWebController::class, 'unmuteParticipant'])->name('unmute');
        Route::post('/end', [RoomWebController::class, 'endCall'])->name('end');
    });
});

Route::middleware([
    CheckAdminRole::class . ':Super Admin,Academic Manager,Moderator,Support Agent,Data Entry,Financial Manager'
])->group(function () {
    Route::get('/content-monitor', [ContentMonitorController::class, 'index'])->name('content.monitor');

    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/{id}', [ReportController::class, 'show'])->name('show');
        Route::post('/{id}/status', [ReportController::class, 'updateStatus'])->name('update-status');
    });
    Route::get('/logs', [DashboardController::class, 'logs'])->name('logs.index');
});

Route::middleware([CheckAdminRole::class . ':Super Admin,Moderator,Academic Manager'])->prefix('punishments')->name('punishments.')->group(function () {
    Route::get('/api-types', [PunishmentController::class, 'getTypesJson'])->name('api.types');
    Route::get('/types', [PunishmentController::class, 'indexTypes'])->name('types.index');
    Route::post('/types', [PunishmentController::class, 'storeType'])->name('types.store');
    Route::get('/active', [PunishmentController::class, 'activePunishments'])->name('active');
    Route::post('/apply', [PunishmentController::class, 'apply'])->name('apply');
    Route::post('/{id}/revoke', [PunishmentController::class, 'revoke'])->name('revoke');
});



Route::middleware([CheckAdminRole::class . ':Super Admin,Academic Manager,Data Entry'])
    ->prefix('dashboard/users')
    ->name('dashboard.users.')
    ->group(function () {
        Route::get('/{type}/{id}/punishments', [UserManagerController::class, 'getUserPunishments'])->name('punishments.list');
        Route::put('/student/{id}/update', [UserManagerController::class, 'updateStudent'])->name('student.update');
        Route::get('/', [UserManagerController::class, 'index'])->name('index');
        Route::get('/filter', [UserManagerController::class, 'filterUsers'])->name('filter');
        Route::get('/fetch-data-by-level', [UserManagerController::class, 'fetchDataByLevel'])->name('fetch.by.level');
        Route::get('/{type}/{id}/details', [UserManagerController::class, 'getUserDetails'])->name('details');
        Route::post('/{type}/{id}/status', [UserManagerController::class, 'updateStatus'])->name('update-status');
        Route::get('/teacher/{id}/setup', [UserManagerController::class, 'teacherSetupForm'])->name('teacher.setup');
        Route::post('/teacher/{id}/setup', [UserManagerController::class, 'completeTeacherSetup'])->name('teacher.setup.submit');
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

Route::middleware([CheckAdminRole::class . ':Super Admin,Academic Manager,Moderator'])->group(function () {
    Route::prefix('content-monitor/conversations')->name('dashboard.conversations.')->group(function () {
        Route::get('/', [ConversationWebController::class, 'index'])->name('index');
        Route::get('/user-profile', [ConversationWebController::class, 'getUserProfile'])->name('user-profile');
        Route::get('/{id}', [ConversationWebController::class, 'show'])->name('show');
        Route::get('/{id}/fetch-messages', [ConversationWebController::class, 'fetchMessages'])->name('fetch-messages');
        Route::post('/{id}/send', [ConversationWebController::class, 'sendMessage'])->name('send-message');
        Route::delete('/messages/{id}', [ConversationWebController::class, 'deleteMessage'])->name('delete-message');
    });
});

Route::get('/send-test-notification', [NotificationController::class, 'testSend']);

use App\Http\Controllers\Dashboard\FinancialDashboardController;

Route::prefix('admin/financial')->name('financial.')->middleware([
    CheckAdminRole::class . ':Super Admin,Financial Manager'
])->group(function () {

    Route::get('/', [FinancialDashboardController::class, 'index'])->name('index');
    Route::get('/donations', [FinancialDashboardController::class, 'donations'])->name('donations');
    Route::get('/salaries', [FinancialDashboardController::class, 'salaries'])->name('salaries');
    Route::get('/rewards', [FinancialDashboardController::class, 'rewards'])->name('rewards');
    Route::get('/transactions', [FinancialDashboardController::class, 'transactions'])->name('transactions');

    Route::post('/salaries/pay', [FinancialDashboardController::class, 'paySalary'])->name('paySalary');
    Route::post('/rewards/{id}/approve', [FinancialDashboardController::class, 'approveReward'])->name('rewards.approve');
    Route::post('/rewards/{id}/reject', [FinancialDashboardController::class, 'rejectReward'])->name('rewards.reject');
});


Route::middleware([CheckAdminRole::class . ':Super Admin,Academic Manager'])->prefix('dashboard/schedules')->name('dashboard.schedules.')->group(function () {
    Route::get('/', [ScheduleController::class, 'index'])->name('index');
    Route::get('/create', [ScheduleController::class, 'create'])->name('create');
    Route::get('/workspace', [ScheduleController::class, 'workspace'])->name('workspace');
    Route::post('/store-bulk', [ScheduleController::class, 'storeBulk'])->name('storeBulk');
    Route::delete('/{id}', [ScheduleController::class, 'destroy'])->name('destroy');
});


Route::middleware([CheckAdminRole::class . ':Super Admin,Academic Manager,Moderator'])->group(function () {
    Route::get('/classes', [ClassController::class, 'index'])->name('classes');
    Route::get('/classes/{id}', [ClassController::class, 'show'])->name('classes.show');
    Route::post('/classes/{id}/teachers/attach', [ClassController::class, 'attachTeacher'])->name('classes.teachers.attach');
    Route::delete('/classes/{class_id}/teachers/{teacher_id}/detach', [ClassController::class, 'detachTeacher'])->name('classes.teachers.detach');
    Route::post('/classes/{class_id}/students/transfer', [ClassController::class, 'transferStudent'])->name('classes.students.transfer');
    Route::post('/classes/{class_id}/students/suggest-points', [ClassController::class, 'suggestPoints'])->name('classes.students.suggest_points');
    Route::get('/students/{student_id}/excuses', [ClassController::class, 'getStudentExcuses'])->name('students.excuses.get');
    Route::post('/excuses/{id}/status', [ClassController::class, 'updateExcuseStatus'])->name('excuses.status.update');
    Route::get('/excuses', [ClassController::class, 'allExcuses'])->name('excuses.index');
    Route::get('/students/{student_id}/absences', [ClassController::class, 'studentAbsences'])->name('students.absences');
    Route::get('/students/{student}/reports', [StudentReportWebController::class, 'index'])->name('students.reports');
});

Route::get('/fcm-test', function () {
    return view('fcm_test');
});
