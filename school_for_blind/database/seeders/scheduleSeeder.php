<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class scheduleSeeder extends Seeder
{
    public function run()
    {
        $schedules = [
            ['class_id' => 1, 'teacher_id' => 1, 'subject_id' => '1', 'day_of_week' =>'1', 'period_number' => 1, 'start_time' => '08:00:00', 'end_time' => '08:45:00'],
            ['class_id' => 1, 'teacher_id' => 1, 'subject_id' => '1', 'day_of_week' =>'2', 'period_number' => 2, 'start_time' => '09:00:00', 'end_time' => '09:45:00'],
            ['class_id' => 1, 'teacher_id' => 2, 'subject_id' => '2',   'day_of_week' =>'3', 'period_number' => 3, 'start_time' => '10:00:00', 'end_time' => '10:45:00'],
            ['class_id' => 1, 'teacher_id' => 3, 'subject_id' => '5',  'day_of_week' =>'4', 'period_number' => 4, 'start_time' => '11:00:00', 'end_time' => '11:45:00'],
            
            ['class_id' => 1, 'teacher_id' => 4, 'subject_id' => '10','day_of_week' => '5', 'period_number' => 1, 'start_time' => '08:00:00', 'end_time' => '08:45:00'],
            ['class_id' => 1, 'teacher_id' => 4, 'subject_id' => '4','day_of_week' =>'1', 'period_number' => 2, 'start_time' => '09:00:00', 'end_time' => '09:45:00'],
            ['class_id' => 1, 'teacher_id' => 2, 'subject_id' => '3',   'day_of_week' =>'2', 'period_number' => 3, 'start_time' => '10:00:00', 'end_time' => '10:45:00'],
        ];

        foreach ($schedules as &$schedule) {
            $schedule['created_at'] = Carbon::now();
            $schedule['updated_at'] = Carbon::now();
        }

        DB::table('schedules')->insert($schedules);
    }
}
