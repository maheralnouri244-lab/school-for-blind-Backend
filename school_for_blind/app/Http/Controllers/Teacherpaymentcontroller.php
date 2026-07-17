<?php

namespace App\Http\Controllers;

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
                $account = $stripe->accounts->create([
                    'type' => 'custom', 
                    'country' => 'DE',
                    'capabilities' => [
                        'transfers' => ['requested' => true],
                    ],
                    'business_profile' => [
                        'name' => $teacher->full_name,
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
            $teacher = DB::table('teachers')->where('id', $teacherId)->first();

            if (!$teacher->stripe_account_id) {
                return response()->json(['error' => 'الأستاذ ليس لديه حساب بنكي مربوط'], 400);
            }

            $schoolWallet = DB::table('school_wallets')->where('id', 1)->first();
            if ($schoolWallet->balance < $amountInEur) {
                return response()->json(['error' => 'رصيد المدرسة غير كافٍ لتحويل الراتب'], 400);
            }

            $stripe = new StripeClient(env('STRIPE_SECRET'));

            $transfer = $stripe->transfers->create([
                'amount' => $amountInCents,
                'currency' => 'eur',
                'destination' => $teacher->stripe_account_id, 
                'description' => 'راتب مستحق للأستاذ: ' . $teacher->full_name,
            ]);

            DB::table('school_wallets')->where('id', 1)->decrement('balance', $amountInEur);

            DB::table('school_transactions')->insert([
                'type' => 'withdrawal',
                'amount' => $amountInEur,
                'description' => 'تم تحويل راتب للأستاذ ' . $teacher->full_name . ' برقم عملية: ' . $transfer->id,
                'created_at' => now(),
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'تم تحويل الراتب بنجاح!'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'فشل التحويل: ' . $e->getMessage()
            ], 500);
        }
    }
}

