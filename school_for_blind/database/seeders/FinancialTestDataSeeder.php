<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class FinancialTestDataSeeder extends Seeder
{
    public function run(): void
    {
        $walletsTable = Schema::hasTable('school_wallets') ? 'school_wallets' : 'wallets';
        DB::table($walletsTable)->updateOrInsert(
            ['id' => 1],
            ['balance' => 15000.50, 'created_at' => now(), 'updated_at' => now()]
        );

        $subjectId = DB::table('subjects')->insertGetId([
            'name' => 'اللغة العربية',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        \Log::info($subjectId);

        $classId = DB::table('classes')->insertGetId([
            'name' => 'التاسع أ',
            'level' => 'ninth',
            'number' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        for ($i = 1; $i <= 3; $i++) {
            $teacherId = DB::table('teachers')->insertGetId([
                'full_name' => 'الأستاذ التجريبي ' . $i,
                'phone' => '090000000' . $i,
                'password' => Hash::make('password'),
                'level' => 'ninth',
                'status' => 'approved',
                'cv_path' => 'dummy.pdf',
                'stripe_account_id' => $i == 1 ? null : 'acct_1dummy' . $i,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            DB::table('teacher_subjects')->insert([
                'teacher_id' => $teacherId,
                'subject_id' => $subjectId,
                'price_for_lesson' => 50,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            for ($r = 1; $r <= 5; $r++) {
                DB::table('rooms')->insert([
                    'creator_type' => 'App\Models\Teacher',
                    'creator_id' => $teacherId,
                    'class_id' => $classId,
                    'subject_id' => $subjectId,
                    'room_name' => 'حصة تجريبية ' . $i . '-' . $r,
                    'status' => 'ended',
                    'started_at' => now()->subDays(2),
                    'ended_at' => now()->subDays(2)->addHours(1),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }

        for ($i = 1; $i <= 3; $i++) {
            $studentId = DB::table('students')->insertGetId([
                'fullname' => 'طالب تجريبي ' . $i,
                'fathersname' => 'أب تجريبي',
                'phone' => '098000000' . $i,
                'parent_phone' => '099000000' . $i,
                'class_id' => $classId,
                'level' => 'ninth',
                'status' => 'approved',
                'DocumentaryEvidence' => 'doc.pdf',
                'points' => 1000,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            DB::table('point_redemption_requests')->insert([
                'student_id' => $studentId,
                'points_to_redeem' => 500,
                'amount_paid' => 50.00,
                'status' => $i == 1 ? 'approved' : 'pending',
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        $txTable = Schema::hasTable('school_transactions') ? 'school_transactions' : 'transactions';
        $targets = ['student', 'teacher', 'parent', 'general'];

        for ($month = 0; $month < 6; $month++) {
            $date = Carbon::now()->subMonths($month)->startOfMonth()->addDays(rand(1, 20));

            for ($d = 0; $d < 3; $d++) {
                $amount = rand(100, 500);

                $donationId = DB::table('donations')->insertGetId([
                    'donor_name' => 'متبرع ' . rand(1, 100),
                    'amount' => $amount,
                    'currency' => 'USD',
                    'stripe_session_id' => 'cs_test_' . rand(1000, 9999) . $month . $d,
                    'status' => 'completed',
                    'donation_target' => $targets[array_rand($targets)],
                    'created_at' => $date,
                    'updated_at' => $date
                ]);

                DB::table($txTable)->insert([
                    'type' => 'deposit',
                    'amount' => $amount,
                    'description' => 'إيداع تبرع من بوابة Stripe',
                    'reference_id' => $donationId,
                    'reference_type' => 'App\Models\Donation',
                    'created_at' => $date,
                    'updated_at' => $date
                ]);
            }

            for ($w = 0; $w < 2; $w++) {
                DB::table($txTable)->insert([
                    'type' => 'withdrawal',
                    'amount' => rand(150, 300),
                    'description' => 'تحويل راتب للأستاذ رقم ' . rand(1, 3),
                    'reference_id' => null,
                    'reference_type' => null,
                    'created_at' => clone $date->addDays(1),
                    'updated_at' => clone $date->addDays(1)
                ]);
            }
        }
    }
}