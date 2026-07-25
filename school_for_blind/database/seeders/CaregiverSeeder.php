<?php

namespace Database\Seeders;

use App\Models\Caregiver;
use App\Models\Student;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CaregiverSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $students = Student::whereNotNull('parent_phone')
            ->whereNull('parent_id')
            ->get();
        foreach ($students as $student) {
            $caregiver = Caregiver::firstOrCreate(
                ['phone' => $student->parent_phone],
                [
                    'password' => Hash::make('12345678'),
                ]
            );
            $student->update([
                'parent_id' => $caregiver->id,
            ]);
        }
    }
}