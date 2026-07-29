<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SchoolWalletSeeder extends Seeder
{
    public function run()
    {
        DB::table('school_wallets')->updateOrInsert(
            ['id' => 1],
            [
                'balance' => 15000, 
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        );

        $transactions = [
            [
                'type' => 'deposit',
                'amount' => 5000,
                'description' => 'تبرع فاعل خير لدعم المستلزمات التعليمية',
                'created_at' => Carbon::now()->subDays(5),
            ],
            [
                'type' => 'deposit',
                'amount' => 12000,
                'description' => 'تبرع من حملة دعم المكفوفين الدولية',
                'created_at' => Carbon::now()->subDays(3),
            ],
            [
                'type' => 'withdrawal', 
                'amount' => 2000,
                'description' => 'راتب الأستاذ أحمد - قسم لغة برايل',
                'created_at' => Carbon::now()->subDays(1),
            ],
        ];

        DB::table('school_transactions')->truncate(); 
        
        DB::table('school_transactions')->insert($transactions);
    }
}