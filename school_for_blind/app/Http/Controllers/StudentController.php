<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Http\Requests\StudentLoginRequest;
use App\Http\Requests\StudentRegisterRequest;
use App\Models\Student;
use App\Services\WhatsAppService;
use App\Traits\UploadFileTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class StudentController extends Controller
{
    use UploadFileTrait;
    public function register(StudentRegisterRequest $request)
    {
        /* $deviceFingerprint = md5($request->ip() . $request->header('User-Agent'));
       $cacheKey = 'otp_verified_' . $request->phone . '_' . $deviceFingerprint;
       $isVerified = Cache::pull($cacheKey);
       if (!$isVerified) {
           return response()->json([
               'message' => 'طلب غير مصرح به، أو انتهت مهلة التحقق.'
           ], 403);
       }*/

        $path = $this->uploadFile($request->file('DocumentaryEvidence'), 'doc');
        Log::info('path for student is ' . $path);
        $existingStudent = clone Student::query();
        if (method_exists($existingStudent, 'withTrashed')) {
            $existingStudent = $existingStudent->withTrashed();
        }
        $studentRecord = $existingStudent->where('phone', $request->phone)->first();
        if ($studentRecord) {
            if (method_exists($studentRecord, 'trashed') && $studentRecord->trashed()) {
                $studentRecord->restore();
            }
            $studentRecord->update([
                'fullname' => $request->fullname,
                'fathersname' => $request->fathersname,
                'parent_phone' => $request->parent_phone,
                'level' => $request->level,
                'DocumentaryEvidence' => $path,
                'status' => 'pending',
                'phone_verified_at' => now(),
            ]);
            $student = $studentRecord;
        } else {
            $student = Student::create([
                'fullname' => $request->fullname,
                'fathersname' => $request->fathersname,
                'phone' => $request->phone,
                'parent_phone' => $request->parent_phone,
                'level' => $request->level,
                'DocumentaryEvidence' => $path,
                'status' => 'pending',
                'phone_verified_at' => now(),
            ]);
        }
        return response()->json([
            'status' => 'success',
            'message' => 'تم حفظ بياناتك بنجاح. حسابك الآن بانتظار مراجعة الإدارة.',
            "data" => [
                "user" => [
                    "id" => $student->id,
                    "fathersname" => $student->fathersname,
                    "fullname" => $student->fullname,
                    "phone" => $student->phone,
                    "status" => $student->status,
                    "DocumentaryEvidence" => method_exists($this, 'getSignedDocumentUrl') ? $this->getSignedDocumentUrl($student->DocumentaryEvidence) : $student->DocumentaryEvidence,
                ],
            ],
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function login(StudentLoginRequest $request, WhatsAppService $whatsApp)
    {
        $student = Student::query()->where('phone', '=', $request->phone)->first();
        if (!$student || $student->status !== 'approved') {
            return response()->json([
                'status' => 'error',
                'message' => 'عذراً، هذا الحساب غير موجود أو لم يتم تفعيله من قبل الإدارة بعد.',
            ], 403, [], JSON_UNESCAPED_UNICODE);
        }


        $dismissalRecord = DB::table('punishables')
            ->join('punishments', 'punishables.punishment_id', '=', 'punishments.id')
            ->where('punishables.punishable_id', $student->id)
            ->where('punishables.punishable_type', get_class($student))
            ->where('punishments.name', 'Dismissal')
            ->where(function ($query) {
                $query->whereNull('punishables.expires_at')
                    ->orWhere('punishables.expires_at', '>', Carbon::now());
            })
            ->select('punishables.expires_at')
            ->first();

        if ($dismissalRecord) {
            $expiresAt = $dismissalRecord->expires_at;

            if (is_null($expiresAt)) {
                $message = 'تم فصل حسابك بشكل نهائي من النظام. يرجى مراجعة الإدارة.';
            } else {
                $timeLeft = Carbon::parse($expiresAt)->locale('ar')->diffForHumans();
                $message = "حسابك مفصول مؤقتاً. ستنتهي العقوبة في: " . $timeLeft;
            }

            return response()->json([
                'status' => 'error',
                'message' => $message,
                'is_dismissed' => true,
                'expires_at' => $expiresAt
            ], 403, [], JSON_UNESCAPED_UNICODE);
        }

        // URL::forceRootUrl('https://barman-wannabe-cape.ngrok-free.dev');
        // URL::forceScheme('https');

        $loginUrl = URL::temporarySignedRoute(
            'student.magic.view',
            now()->addMinutes(15),
            ['id' => $student->id]
        );

        try {

            $whatsApp->sendMagicLink($student->phone, $student->fullname, $loginUrl);

            return response()->json([
                'status' => 'success',
                'message' => 'تم إرسال رابط الدخول المباشر إلى رقمك على واتساب بنجاح',
            ], 200, [], JSON_UNESCAPED_UNICODE);

        } catch (\Exception $e) {

            Log::error("WhatsApp Login Error: " . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'حدث خطأ أثناء إرسال الرسالة، يرجى المحاولة لاحقاً.',
            ], 500, [], JSON_UNESCAPED_UNICODE);
        }
    }
    public function magicLogin(Request $request, $id)
    {
        if (!$request->hasValidSignature()) {
            return response()->json(['message' => 'عذراً، الرابط منتهي الصلاحية أو غير صالح.'], 401);
        }
        $student = Student::findOrFail($id);
        //$student->DocumentaryEvidence = $student->getFullUrl($student->DocumentaryEvidence);
        $token = $student->createToken('student_access_token')->plainTextToken;
        return response()->json([
            'status' => 'success',
            'message' => 'تم تسجيل الدخول بنجاح',
            'user' => $student,
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function logout(Request $request): JsonResponse
    {
        try {
            $student = $request->user();
            if (!$student) {
                return response()->json([
                    'success' => false,
                    'message' => 'المستخدم غير موجود أو غير مصرح له.'
                ], Response::HTTP_UNAUTHORIZED);
            }
            $student->timestamps = false;
            $student->update(['fcm_token' => null]);
            $student->currentAccessToken()->delete();
            return response()->json([
                'success' => true,
                'message' => 'تم تسجيل الخروج بنجاح ',
                'meta' => [
                    'timestamp' => now()->toIso8601String(),
                    'student_id' => $student->id
                ]
            ], Response::HTTP_OK, [], JSON_UNESCAPED_UNICODE);

        } catch (\Exception $e) {
            Log::error("Logout Failed for Student: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'عذراً، حدث خطأ غير متوقع في الخادم أثناء محاولة تسجيل الخروج.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR, [], JSON_UNESCAPED_UNICODE);
        }
    }

    public function checkDismissalStatus(Request $request): JsonResponse
    {
        $student = $request->user();

        $isDismissed = DB::table('punishables')
            ->join('punishments', 'punishables.punishment_id', '=', 'punishments.id')
            ->where('punishables.punishable_id', $student->id)
            ->where('punishables.punishable_type', get_class($student))
            ->where('punishments.name', 'Dismissal')
            ->where(function ($query) {
                $query->whereNull('punishables.expires_at')
                    ->orWhere('punishables.expires_at', '>', Carbon::now());
            })
            ->exists();

        if ($isDismissed) {
            if (method_exists($student, 'currentAccessToken') && $student->currentAccessToken()) {
                $student->currentAccessToken()->delete();
            }
        }

        return response()->json([
            'is_dismissed' => $isDismissed
        ]);
    }
}
