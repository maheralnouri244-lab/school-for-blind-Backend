<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnnouncementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $timetableData = [
            "columns" => [
                "الحصة",
                "الأحد",
                "الإثنين",
                "الثلاثاء",
                "الأربعاء",
                "الخميس"
            ],
            "rows" => [
                [
                    "period" => "الأولى",
                    "sunday" => "رياضيات",
                    "monday" => "فيزياء",
                    "tuesday" => "كيمياء",
                    "wednesday" => "لغة عربية",
                    "thursday" => "إنجليزي"
                ],
                [
                    "period" => "الثانية",
                    "sunday" => "تاريخ",
                    "monday" => "جغرافيا",
                    "tuesday" => "رياضيات",
                    "wednesday" => "علم أحياء",
                    "thursday" => "لغة عربية"
                ],
                [
                    "period" => "الثالثة",
                    "sunday" => "إنجليزي",
                    "monday" => "لغة عربية",
                    "tuesday" => "فرنسي",
                    "wednesday" => "رياضيات",
                    "thursday" => "فيزياء"
                ],
                [
                    "period" => "الرابعة",
                    "sunday" => "علم أحياء",
                    "monday" => "تربية وطنية",
                    "tuesday" => "جغرافيا",
                    "wednesday" => "إنجليزي",
                    "thursday" => "كيمياء"
                ],
                [
                    "period" => "الخامسة",
                    "sunday" => "تربية دينية",
                    "monday" => "رياضة",
                    "tuesday" => "فنية",
                    "wednesday" => "تاريخ",
                    "thursday" => "فرنسي"
                ]
            ]
        ];

        DB::table('announcements')->insert([
            'type' => 'school_timetable',
            'title' => 'برنامج الدوام الأسبوعي لعام 2026',
            'content' => json_encode($timetableData, JSON_UNESCAPED_UNICODE),
            'level' => 'all',
            'target_audience' => 'teacher',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('announcements')->insert([
            'type' => 'school_timetable',
            'title' => 'برنامج الدوام الأسبوعي لعام 2026',
            'content' => json_encode($timetableData, JSON_UNESCAPED_UNICODE),
            'level' => 'all',
            'target_audience' => 'student',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}