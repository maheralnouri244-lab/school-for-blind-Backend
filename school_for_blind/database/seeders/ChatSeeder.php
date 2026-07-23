<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Conversation;
use App\Models\Teacher;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ChatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $teachers = Teacher::skip(1)->take(5)->get();

        foreach ($teachers as $teacher) {
            $subjects = $teacher->subjects()->get();
            foreach ($subjects as $subject) {
                $channel = Conversation::firstOrCreate([
                    'type' => 'channel',
                    'teacher_id' => $teacher->id,
                    'subject_id' => $subject->id,
                ], [
                    'name' => 'قناة مادة ' . $subject->name . ' - ' . $teacher->full_name,
                ]);

                Conversation::firstOrCreate([
                    'type' => 'discussion',
                    'teacher_id' => $teacher->id,
                    'subject_id' => $subject->id,
                    'parent_id' => $channel->id,
                ], [
                    'name' => 'مناقشة مادة ' . $subject->name . ' - ' . $teacher->full_name,
                ]);
            }
        }
        $admins = Admin::whereIn('role', [
            'Super Admin',
            'Academic Manager',
        ])->get();

        foreach ($admins as $admin) {
            \Log::info($admin);
            Conversation::firstOrCreate([
                'type' => 'teacher_admin',
                'teacher_id' => $teacher->id,
                'admin_id' => $admin->id,
            ], [
                'name' => 'محادثة الإدارة - ' . $admin->role,
            ]);
        }
    }
}
