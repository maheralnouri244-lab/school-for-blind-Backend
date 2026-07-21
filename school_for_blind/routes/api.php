<?php

use App\Http\Controllers\Admin\TeacherTransferController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\BookmarkController;
use App\Http\Controllers\CaregiverController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\LiveKitWebhookController;
use App\Http\Controllers\MagicLoginController;
use App\Http\Controllers\OtpController;
use App\Http\Controllers\ParentReportController;
use App\Http\Controllers\PointRedemptionController;
use App\Http\Controllers\PunishmentController;
use App\Http\Controllers\QuestionBankController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\StripeWebhookController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StudentExamController;
use App\Http\Controllers\StudentpastexamController;
use App\Http\Controllers\StudentQuizController;
use App\Http\Controllers\SupportTicketController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\TeacherExamController;
use App\Http\Controllers\TeacherExamcorrectController;
use App\Http\Controllers\Teacherpaymentcontroller;
use App\Http\Controllers\TeacherQuizController;
use App\Http\Middleware\CheckCallCreatorRole;
use App\Http\Middleware\CheckIsStudent;
use App\Http\Middleware\CheckPunishment;
use App\Http\Middleware\CheckUserType;
use App\Http\Middleware\IsTeacher;
use App\Http\Middleware\PreventStudentCallActions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('verify-otp', [OtpController::class, 'verify']);
Route::post('register', [StudentController::class, 'register']);
Route::post('login', [StudentController::class, 'login']);
Route::middleware(['auth:sanctum', 'CheckIsStudent'])->group(function () {

    Route::post('/logout', [StudentController::class, 'logout']);
});
Route::post('/auth/exchange-token', [MagicLoginController::class, 'exchangeToken']);
// Route::get('magic-login/{id}', [StudentController::class, 'magicLogin'])
//     ->name('student.magic.login');
//     ->middleware('signed');

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('otp')->controller(OtpController::class)->group(function () {
    Route::post('send', 'sendOtp')->name('otp.send')->middleware('throttle:3,1');
    Route::post('verify', 'verifyOtp')->name('otp.verify');
});

Route::prefix('teacher')->controller(TeacherController::class)->group(function () {
    Route::post('register', 'register')->name('users.register');
    Route::post('login', 'login')->name('users.login');
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', 'logout')->name('teachers.logout');
        Route::get('info', 'info')->name('teachers.info');
        Route::get('all-subjects/statistics', 'getAllSubjectsStats')->name('teachers.stats');
        // Route::get('cv', 'showCv')->name('teachers.cv');
    });
});

Route::post('/caregiver/login', [CaregiverController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/caregiver/logout', [CaregiverController::class, 'logout']);
});

Route::post('/donation/checkout', [DonationController::class, 'checkout']);
Route::post('/donation/confirm', [DonationController::class, 'confirmPayment']);
Route::get('/donation/success', [DonationController::class, 'success'])->name('donation.success');
Route::get('/donation/cancel', [DonationController::class, 'cancel'])->name('donation.cancel');
Route::middleware('auth:sanctum')->group(function () {});
Route::post('/point-redemption/request', [PointRedemptionController::class, 'store'])->middleware('auth:sanctum');
Route::prefix('admin/point-redemption')->middleware(['auth:sanctum'])->group(function () {
    Route::post('/{redemptionRequest}/approve', [PointRedemptionController::class, 'approve']);
    Route::get('/{redemptionRequest}/reject', [PointRedemptionController::class, 'reject']);
});
Route::prefix('call')->middleware('auth:sanctum')->group(function () {
    Route::get('/active-calls', [RoomController::class, 'getActiveCallsForStudent']);
    Route::post('/start', [RoomController::class, 'startCall'])->middleware(CheckCallCreatorRole::class);
    Route::post('/join', [RoomController::class, 'joinCall']);
    Route::middleware(PreventStudentCallActions::class)->group(function () {
        Route::post('/kick', [RoomController::class, 'kickParticipant']);
        Route::post('/mute', [RoomController::class, 'muteParticipant']);
        Route::post('/end', [RoomController::class, 'endCall']);
        Route::post('/unmute-participant', [RoomController::class, 'unmuteParticipant']);
    });
});

Route::prefix('quizzes')->group(function () {
    Route::middleware(['auth:sanctum', 'isTeacher'])->group(function () {
        Route::post('/', [QuizController::class, 'store']);
        Route::get('/teacher/list', [QuizController::class, 'index']);
        Route::post('/{id}', [QuizController::class, 'update']);
        Route::delete('/{id}', [QuizController::class, 'destroy']);
        Route::get('{lessonId}/quiz', [QuizController::class, 'getQuizByLesson']);
        Route::post('/{quizId}/students/{studentId}/grade', [QuizController::class, 'gradeTextAnswers']);
        Route::get('/teacher/pending-grading', [QuizController::class, 'getQuizzesPendingGrading']);
        Route::get('/{quizId}/submissions', [QuizController::class, 'getQuizSubmissions']);
        Route::get('/{quizId}/students/{studentId}/pending-answers', [QuizController::class, 'getPendingTextAnswers']);
    });
    // Route::middleware(['auth:sanctum', 'CheckIsStudent'])->group(function () {
    // });
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/{id}/submit', [QuizController::class, 'submitQuiz']);
        Route::get('/{id}/student-view', [QuizController::class, 'getStudentQuiz']);
        Route::get('/{id}', [QuizController::class, 'show']);
        Route::get('/{quiz_id}/students/{student_id}/answers', [QuizController::class, 'getStudentAnswers']);
    });
});

Route::middleware(['auth:sanctum', 'isTeacher'])->prefix('exam')->controller(TeacherExamController::class)->group(function () {
    Route::post('/', 'store');
    Route::get('/pending-grading', 'getExamsPendingGrading');
    Route::get('/{examId}/submissions', 'getExamSubmissions');
    Route::get('/{examId}/students/{studentId}/pending-answers', 'getPendingTextAnswers');
    Route::post('/{examId}/students/{studentId}/grade', 'gradeTextAnswers');
    Route::get('/my-exams', 'myExams');
    Route::get('/my-exams/{exam_id}', 'show');
});

Route::middleware('auth:sanctum')
    ->prefix('lessons')
    ->controller(LessonController::class)
    ->group(function () {
        Route::get('/', 'index');
        Route::post('/', 'store');
        Route::get('{lesson}', 'show');
        Route::match(['put', 'patch'], '{lesson}', 'update');
        Route::delete('{lesson}', 'destroy');
    });
Route::post('/announcements', [AnnouncementController::class, 'store']);
Route::get('/announcements', [AnnouncementController::class, 'index']);
Route::get('/announcements/exam/{id}', [AnnouncementController::class, 'showExam']);
Route::get('/announcements/school-timetable/first', [AnnouncementController::class, 'firstSchoolTimetable']);


Route::prefix('student/quizzes')->group(function () {

    Route::get('{id}/questions', [StudentQuizController::class, 'getQuizQuestions']);
});
Route::middleware('auth:sanctum')->group(function () {
    //  Route::post('search-info', [StudentQuizController::class, 'getQuizInfoByNames']);
    // Route::post('student/quizzes/search-info', [StudentQuizController::class, 'getQuizInfo']);
    Route::post('student/quizzes/search-info', [StudentQuizController::class, 'getQuizInfoByNames']);
});



Route::post('/get-quiz-info', [QuizController::class, 'getQuizInfoByNames']);
Route::middleware(['auth:sanctum', IsTeacher::class])->prefix('question-bank')->group(function () {
    Route::get('/', [QuestionBankController::class, 'index']);
    Route::post('/', [QuestionBankController::class, 'store']);
    Route::get('/{id}', [QuestionBankController::class, 'show']);
    Route::delete('/{id}', [QuestionBankController::class, 'destroy']);
});


Route::middleware(['auth:sanctum', CheckUserType::class . ':admin'])->prefix('admin/teachers')->group(function () {
    Route::post('/transfer-assets', [TeacherTransferController::class, 'transferAssets']);
});
Route::middleware('auth:sanctum')->group(function () {

    Route::post('quiz/submit', [StudentQuizController::class, 'submitQuiz']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/subjects/{id}/lessons', [LessonController::class, 'getLessonsBySubject']);
    Route::get('student/quizzes/{quizId}/review', [StudentQuizController::class, 'getQuizReview']);
});
Route::get('/lessons/{id}/record', [LessonController::class, 'getLessonRecord']);
Route::get('/subjects/{id}/lessons/count', [LessonController::class, 'getLessonsCountBySubject']);
Route::get('/subjects/{id}/lessons/progress', [LessonController::class, 'getLessonsProgress']);


Route::middleware('auth:sanctum')->group(function () {

    Route::post('/favorites/toggle', [FavoriteController::class, 'toggle']);
    Route::post('favorites/add', [FavoriteController::class, 'addToFavorite']);

    Route::get('/favorites/lessons', [FavoriteController::class, 'favoriteLessons']);

    Route::get('/favorites/quizzes', [FavoriteController::class, 'favoriteQuizzes']);
    Route::get('favorites/exams', [FavoriteController::class, 'favoriteExams']);
    Route::get('favorites/past-exams', [FavoriteController::class, 'favoritePastExams']);

    Route::get('/favorites/all', [FavoriteController::class, 'allFavorites']);
});
Route::post('/favorites/remove', [FavoriteController::class, 'remove'])
    ->middleware('auth:sanctum');


Route::middleware(['auth:sanctum', CheckUserType::class . ':admin'])->prefix('admin/punishments')->group(function () {
    Route::post('/apply', [PunishmentController::class, 'applyPunishment']);
    Route::patch('/{id}/revoke', [PunishmentController::class, 'revokePunishment']);
});


Route::middleware(['auth:sanctum', CheckPunishment::class . ':Report Ban'])->group(function () {
    Route::post('/reports', [ReportController::class, 'store']);
});

Route::middleware(['auth:sanctum', CheckUserType::class . ':admin'])->prefix('admin/reports')->group(function () {
    Route::get('/', [ReportController::class, 'index']);
    Route::patch('/{id}/status', [ReportController::class, 'updateStatus']);
});
Route::post('/stripe/webhook', [StripeWebhookController::class, 'handleWebhook']);

Route::post('/livekit/webhook', [LiveKitWebhookController::class, 'handle']);

Route::middleware(['auth:sanctum', IsTeacher::class])->prefix('teacher/channels')->group(function () {
    Route::get('/', [ConversationController::class, 'getTeacherChannels']);
    Route::get('/admin-chats', [ConversationController::class, 'getTeacherAdminConversations']);
    Route::get('/{conversationId}/messages', [ConversationController::class, 'getMessages']);
    Route::post('/{conversationId}/messages', [ConversationController::class, 'sendMessage'])
        ->middleware(CheckPunishment::class . ':Mute');
    Route::post('/{conversationId}/read', [ConversationController::class, 'markAsRead']);
});

Route::middleware(['auth:sanctum', CheckIsStudent::class])->prefix('student/channels')->group(function () {
    Route::get('/', [ConversationController::class, 'getStudentChannels']);
    Route::get('/{conversationId}/messages', [ConversationController::class, 'getMessages']);
    Route::post('/{conversationId}/messages', [ConversationController::class, 'sendMessage'])
        ->middleware(CheckPunishment::class . ':Mute');
    Route::post('/{conversationId}/read', [ConversationController::class, 'markAsRead']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::delete('/messages/{messageId}', [ConversationController::class, 'deleteMessage']);
    Route::post('/messages/{messageId}/report', [ConversationController::class, 'reportMessage']);
});


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/student/submissions', [StudentQuizController::class, 'getStudentSubmissions']);
});
Route::post('/support-tickets', [SupportTicketController::class, 'store'])->middleware('auth:sanctum');
Route::get('/past-exams', [StudentpastexamController::class, 'getPastExamsBySubject']);

Route::get('/past-exams/{id}/questions', [StudentpastexamController::class, 'getQuestionsByPastExam']);

Route::get('/past-exams/{id}/solutions', [StudentpastexamController::class, 'getPastExamWithSolutions']);

Route::get('/exams', [StudentExamController::class, 'getExamsBySubject']);
Route::get('/exams/{id}/questions', [StudentExamController::class, 'getQuestionsByExam']);
Route::get('/exams/{id}/solutions', [StudentExamController::class, 'getExamWithSolutions']);
Route::post('/exams/submit-answer', [StudentExamController::class, 'submitAnswer'])->middleware('auth:sanctum');
Route::get('/submissions/{id}/details', [StudentExamController::class, 'getSubmissionDetails'])->middleware('auth:sanctum');
Route::post('/exams/submit', [StudentExamController::class, 'submitExam'])->middleware('auth:sanctum');
Route::get('/student/exam-details/{id}', [StudentExamController::class, 'getExamDetails']);

Route::middleware('auth:sanctum')->group(function () {

    Route::get('recordings/{recordingId}/bookmarks', [BookmarkController::class, 'index']);

    Route::post('bookmarks', [BookmarkController::class, 'store']);

    Route::put('bookmarks/{id}', [BookmarkController::class, 'update']);

    Route::delete('bookmarks/{id}', [BookmarkController::class, 'destroy']);

});



Route::get('/teacher/pending-essay-answers', [TeacherQuizController::class, 'getPendingEssayAnswers'])->middleware(['auth:sanctum', IsTeacher::class]);
Route::post('/teacher/grade-full-quiz-submission', [TeacherQuizController::class, 'gradeFullQuizSubmission'])->middleware(['auth:sanctum', IsTeacher::class]);

Route::middleware('auth:teacher')->group(function () {
    Route::get('/teacher/pending-exams', [TeacherExamcorrectController::class, 'getPendingExamEssayAnswers']);

    Route::post('/teacher/grade-exam', [TeacherExamcorrectController::class, 'gradeFullExamSubmission']);
});


Route::post('/transfer/salary/teacher', [Teacherpaymentcontroller::class, 'setupTeacherBank']);/*->middleware(['auth:sanctum', CheckUserType::class . ':admin']);*/
Route::post('/teacher/pay-salary', [TeacherPaymentController::class, 'payTeacherSalary']);


Route::middleware(['auth:sanctum', 'isparent'])->prefix('parent')->group(function () {
    Route::get('/reports/daily', [ParentReportController::class, 'getDailyReport']);
    Route::get('/reports/monthly', [ParentReportController::class, 'getMonthlyReport']);
    Route::get('/reports/yearly', [ParentReportController::class, 'getYearlyReport']);
    Route::post('/reports/absence-excuse', [ParentReportController::class, 'submitAbsenceExcuse']);
    Route::post('/reports/objection', [ParentReportController::class, 'submitObjection']);
    Route::get('/reports/student/{studentId}/subject/{subjectId}', [ParentReportController::class, 'getSubjectGrades']);
});