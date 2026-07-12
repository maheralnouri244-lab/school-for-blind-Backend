<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SupportTicket;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Caregiver;
use App\Models\Admin;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class SupportTicketSeeder extends Seeder
{
    public function run(): void
    {
        $sourcePath = database_path('seeders/files/support_team_image.jpg');
        $storageDir = 'support_attachments';
        $imagePath = null;

        if (File::exists($sourcePath)) {
            Storage::disk('public')->makeDirectory($storageDir);
            $destinationPath = storage_path('app/public/' . $storageDir . '/support_team_image.jpg');
            File::copy($sourcePath, $destinationPath);
            $imagePath = $storageDir . '/support_team_image.jpg';
        }

        $students = Student::inRandomOrder()->take(5)->get();
        $teachers = Teacher::inRandomOrder()->take(5)->get();
        $caregivers = Caregiver::inRandomOrder()->take(5)->get();

        $admin = Admin::first();

        $allUsers = collect();
        foreach ($students as $student) $allUsers->push($student);
        foreach ($teachers as $teacher) $allUsers->push($teacher);
        foreach ($caregivers as $caregiver) $allUsers->push($caregiver);

        if ($allUsers->isEmpty()) {
            $this->command->error('لا يوجد أي مستخدمين (طلاب/أساتذة/أهل) في قاعدة البيانات لإنشاء تذاكر لهم.');
            return;
        }

        $departments = [null, 'Super Admin', 'Academic Manager', 'Moderator', 'Financial Manager', 'Data Entry'];
        $statuses = ['open', 'in_progress', 'resolved', 'closed'];
        $priorities = ['low', 'medium', 'high', 'urgent'];

        $messages = [
            'الموقع لا يعمل معي بشكل جيد من الجوال، تظهر شاشة بيضاء.',
            'عندي مشكلة في رفع الوظيفة، تظهر رسالة خطأ بأن الملف كبير جداً.',
            'كيف يمكنني سحب أرباحي من المحفظة المالية؟ لم أجد الزر.',
            'يوجد طالب يزعجني في مجموعة المناقشة ويستخدم ألفاظ مسيئة.',
            'نسيت كلمة المرور ولا تصلني رسالة الـ OTP.'
        ];

        for ($i = 0; $i < 40; $i++) {
            $sender = $allUsers->random();
            $senderType = get_class($sender);

            $department = $departments[array_rand($departments)];
            $status = $department === null ? 'open' : $statuses[array_rand($statuses)];
            $randomDate = Carbon::now()->subDays(rand(0, 30));

            SupportTicket::create([
                'sender_type' => $senderType,
                'sender_id' => $sender->id,
                'message' => $messages[array_rand($messages)],
                'attachment_path' => (rand(0, 1) === 1) ? $imagePath : null,
                'priority' => $priorities[array_rand($priorities)],
                'status' => $status,
                'assigned_department' => $department,
                'classified_by' => $department ? ($admin->id ?? null) : null,
                'created_at' => $randomDate,
            ]);
        }
    }
}