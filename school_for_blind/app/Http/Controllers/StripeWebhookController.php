<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;

class StripeWebhookController extends Controller
{
    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        
        $endpointSecret = env('STRIPE_WEBHOOK_SECRET'); 

        $event = null;

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
        } catch (\UnexpectedValueException $e) {
            return response()->json(['error' => 'حزمة البيانات غير صالحة'], 400);
        } catch (SignatureVerificationException $e) {
            Log::critical('تنبيه أمني: فشل التحقق من توقيع Stripe Webhook!');
            return response()->json(['error' => 'التوقيع الرقمي غير صحيح'], 400);
        }

        switch ($event->type) {
            case 'payment_intent.succeeded':
                $paymentIntent = $event->data->object; 
                $this->processSuccessfulPayment($paymentIntent);
                break;

            case 'payment_intent.payment_failed':
                $paymentIntent = $event->data->object;
                Log::warning("فشلت عملية الدفع للـ Intent ID: " . $paymentIntent->id);
                break;
        }

        return response()->json(['status' => 'success'], 200);
    }

    private function processSuccessfulPayment($paymentIntent)
    {
        try {
            DB::transaction(function () use ($paymentIntent) {
                
                $donation = DB::table('donations')
                    ->where('stripe_session_id', $paymentIntent->id)
                    ->lockForUpdate()
                    ->first();

                if (!$donation || $donation->status === 'completed') {
                    return;
                }

                DB::table('donations')
                    ->where('id', $donation->id)
                    ->update([
                        'status' => 'completed',
                        'updated_at' => now()
                    ]);

                DB::table('school_wallets')
                    ->where('id', 1)
                    ->increment('balance', $donation->amount);

                DB::table('school_transactions')->insert([
                    'type'           => 'deposit',
                    'amount'         => $donation->amount,
                    'description'    => "تبرع ناجح وآمن ومؤكد عبر الـ Webhook من: " . ($donation->donor_name ?? 'فاعل خير') . " بقيمة: {$donation->amount}",
                    'reference_id'   => $donation->id,
                    'reference_type' => 'App\Models\Donation',
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ]);

                Log::info("تمت معالجة التبرع بنجاح عبر الـ Webhook للـ Intent: " . $paymentIntent->id);
            });
        } catch (\Exception $e) {
            Log::error("خطأ أثناء معالجة الـ Webhook المالي: " . $e->getMessage());
        }
    }
}