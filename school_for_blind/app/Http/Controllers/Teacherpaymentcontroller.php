<?php

namespace App\Http\Controllers;

use App\Events\SalaryTransferred;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Stripe\StripeClient;

class Teacherpaymentcontroller extends Controller
{
    public function setupTeacherBank(Request $request)
    {
        $request->validate([
            'teacher_id' => 'required|exists:teachers,id',
            'iban' => 'required|string', 
        ]);

        $teacherId = $request->teacher_id;
        $iban = $request->iban;

        $stripe = new StripeClient(env('STRIPE_SECRET'));

        try {
            $teacher = DB::table('teachers')->where('id', $teacherId)->first();
            $stripeAccountId = $teacher->stripe_account_id;

            if (!$stripeAccountId) {
                $nameParts = explode(' ', $teacher->full_name);
                $firstName = $nameParts[0] ?? 'Teacher';
                $lastName = $nameParts[1] ?? 'Name';

                $account = $stripe->accounts->create([
                    'type' => 'custom', 
                    'country' => 'DE', 
                    'capabilities' => [
                        'transfers' => ['requested' => true],
                    ],
                    'business_type' => 'individual',
                    'business_profile' => [
                        'mcc' => '8211', 
                        'url' => 'https://example-school.com', 
                    ],
                    'individual' => [
                        'first_name' => $firstName,
                        'last_name' => $lastName,
                        'dob' => ['day' => 1, 'month' => 1, 'year' => 1990],
                        'address' => [
                            'line1' => 'Alexanderplatz 1',
                            'city' => 'Berlin',
                            'postal_code' => '10178',
                        ],
                    ],
                    'tos_acceptance' => [
                        'date' => time(),
                        'ip' => '127.0.0.1', 
                    ],
                ]);

                $stripeAccountId = $account->id;

                DB::table('teachers')
                    ->where('id', $teacherId)
                    ->update(['stripe_account_id' => $stripeAccountId]);
            }

            $stripe->accounts->createExternalAccount(
                $stripeAccountId,
                [
                    'external_account' => [
                        'object' => 'bank_account',
                        'country' => 'DE', 
                        'currency' => 'eur',
                        'account_number' => $iban,
                    ],
                ]
            );

            return response()->json([
                'status' => 'success',
                'message' => 'تم ربط الحساب البنكي للأستاذ وتشفيره في Stripe بنجاح!'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'حدث خطأ أثناء معالجة بيانات البنك: ' . $e->getMessage()
            ], 500);
        }
    }

    public function payTeacherSalary(Request $request)
    {
        $request->validate([
            'teacher_id' => 'required|exists:teachers,id',
            'amount' => 'required|numeric|min:1', 
        ]);

        $teacherId = $request->teacher_id;
        $amountInEur = $request->amount;
        $amountInCents = $amountInEur * 100; 

      try {
            $teacher = Teacher::find($teacherId);

            if (!$teacher->stripe_account_id) {
                return response()->json(['error' => 'الأستاذ ليس لديه حساب بنكي مربوط'], 400);
            }

            DB::transaction(function () use ($teacher, $amountInEur, $amountInCents) {
                
                $schoolWallet = DB::table('school_wallets')
                    ->where('id', 1)
                    ->lockForUpdate()
                    ->first();

                if (!$schoolWallet) {
                    throw new \Exception('محفظة المدرسة غير موجودة في النظام الداخلي', 404);
                }

                if ($schoolWallet->balance < $amountInEur) {
                    throw new \Exception('رصيد المدرسة غير كافٍ لتحويل الراتب', 400);
                }

                $stripe = new StripeClient(env('STRIPE_SECRET'));
                $transfer = $stripe->transfers->create([
                    'amount' => $amountInCents,
                    'currency' => 'eur',
                    'destination' => $teacher->stripe_account_id, 
                    'description' => 'راتب مستحق للأستاذ: ' . $teacher->full_name,
                ]);

                DB::table('school_wallets')
                    ->where('id', 1)
                    ->decrement('balance', $amountInEur);

                DB::table('school_transactions')->insert([
                    'type'           => 'withdrawal',
                    'amount'         => $amountInEur,
                    'description'    => 'تم تحويل راتب للأستاذ ' . $teacher->full_name . ' برقم عملية: ' . $transfer->id,
                    'reference_id'   => $teacher->id,
                    'reference_type' => 'App\Models\Teacher', 
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ]);
            });
event(new SalaryTransferred($teacher, $amountInEur));
            return response()->json([
                'status' => 'success',
                'message' => 'تم تحويل الراتب بنجاح!'
            ], 200);

        } catch (\Stripe\Exception\ApiErrorException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Stripe Error: ' . $e->getMessage()
            ], 400);
        } catch (\Exception $e) {
            $statusCode = $e->getCode() ?: 500;
            $statusCode = ($statusCode >= 400 && $statusCode < 600) ? $statusCode : 500;
            
            return response()->json([
                'status' => 'error',
                'message' => 'System Error: ' . $e->getMessage()
            ], $statusCode);
        }
    }
}