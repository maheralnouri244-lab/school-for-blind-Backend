<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Seeders\AdminSeeder;
use Database\Seeders\ClassesTableSeeder;
use Database\Seeders\LessonSeeder;
use Database\Seeders\PunishmentSeeder;
use Database\Seeders\QuizSeeder;
use Database\Seeders\SpecialTeacherSeeder;
use Database\Seeders\StudentSeeder;
use Database\Seeders\SubjectLessonsCountSeeder;
use Database\Seeders\SubjectSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call([
            ClassesTableSeeder::class,
            SubjectSeeder::class,
            SpecialTeacherSeeder::class,
            ContentSeeder::class,
            AdminSeeder::class,
            TeacherSeeder::class,
            PastExamSeeder::class,
            StudentSeeder::class,
            scheduleSeeder::class,
            ExamSeeder::class,
            LessonSeeder::class,
            SubjectLessonsCountSeeder::class,
            CaregiverSeeder::class,
            AnnouncementSeeder::class,
            PunishmentSeeder::class,
            QuizSeeder::class,
            // QuizSubmissionSeeder::class,
            SupportTicketSeeder::class,
            ParentReportTestDataSeeder::class,
            ChatSeeder::class,
            SchoolWalletSeeder::class,
            // AssessmentSubmissionSeeder::class,
            FinancialTestDataSeeder::class,
        ]);

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}
