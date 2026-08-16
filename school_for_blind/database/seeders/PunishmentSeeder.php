<?php

namespace Database\Seeders;

use App\Models\Punishment;
use Illuminate\Database\Seeder;

class PunishmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $punishments = [
            // ==========================================
            // عقوبات الطلاب (student)
            // ==========================================
            [
                'name' => 'Mute',
                'level' => 1,
                'description' => 'منع من المحادثات - درجة أولى (لمدة يوم واحد)',
                'duration_minutes' => 1440, // 24 ساعة × 60 دقيقة
                'target_type' => 'student',
            ],
            [
                'name' => 'Mute',
                'level' => 2,
                'description' => 'منع من المحادثات - درجة ثانية (لمدة 3 أيام)',
                'duration_minutes' => 4320,
                'target_type' => 'student',
            ],
            [
                'name' => 'Mute',
                'level' => 3,
                'description' => 'منع من المحادثات - درجة ثالثة (لمدة أسبوع)',
                'duration_minutes' => 10080,
                'target_type' => 'student',
            ],
            [
                'name' => 'Warning',
                'level' => 1,
                'description' => 'إنذار أكاديمي (تأخر عن درس يقام حاليا)',
                'duration_minutes' => null,
                'target_type' => 'student',
            ],
            [
                'name' => 'Warning',
                'level' => 1,
                'description' => 'إنذار أكاديمي أو سلوكي - درجة أولى (تنبيه أول)',
                'duration_minutes' => null,
                'target_type' => 'student',
            ],
            [
                'name' => 'Warning',
                'level' => 2,
                'description' => 'إنذار أكاديمي أو سلوكي - درجة ثانية (تنبيه ثاني خطير)',
                'duration_minutes' => null,
                'target_type' => 'student',
            ],
            [
                'name' => 'Warning',
                'level' => 3,
                'description' => 'إنذار أكاديمي أو سلوكي - درجة ثالثة (تنبيه نهائي قبل الحظر)',
                'duration_minutes' => null,
                'target_type' => 'student',
            ],
            [
                'name' => 'Report Ban',
                'level' => 1,
                'description' => 'منع من إرسال البلاغات - درجة أولى (لمدة 3 أيام)',
                'duration_minutes' => 4320,
                'target_type' => 'student',
            ],
            [
                'name' => 'Report Ban',
                'level' => 2,
                'description' => 'منع من إرسال البلاغات - درجة ثانية (لمدة أسبوع)',
                'duration_minutes' => 10080,
                'target_type' => 'student',
            ],
            [
                'name' => 'Report Ban',
                'level' => 3,
                'description' => 'منع من إرسال البلاغات - درجة ثالثة (لمدة شهر كامل)',
                'duration_minutes' => 43200,
                'target_type' => 'student',
            ],
            [
                'name' => 'Dismissal',
                'level' => 1,
                'description' => 'فصل لمدة 3 ايام من النظام وتجميد الحساب',
                'duration_minutes' => 4320,
                'target_type' => 'student',
            ],
            [
                'name' => 'Dismissal',
                'level' => 2,
                'description' => 'فصل لمدة اسبوع من النظام وتجميد الحساب',
                'duration_minutes' => 10080,
                'target_type' => 'student',
            ],
            [
                'name' => 'Dismissal',
                'level' => 3,
                'description' => 'فصل نهائي من النظام وتجميد الحساب',
                'duration_minutes' => null,
                'target_type' => 'student',
            ],

            // ==========================================
            // عقوبات الأساتذة (teacher)
            // ==========================================
            [
                'name' => 'Teacher Warning',
                'level' => 1,
                'description' => 'إنذار إداري - تأخر عن بدء الحصة في وقتها',
                'duration_minutes' => null,
                'target_type' => 'teacher',
            ],
            [
                'name' => 'Salary Deduction',
                'level' => 2,
                'description' => 'خصم من الراتب - درجة أولى بسبب مخالفة القوانين',
                'duration_minutes' => null,
                'target_type' => 'teacher',
            ],

            // ==========================================
            // عقوبات عامة للجميع (all)
            // ==========================================
            [
                'name' => 'General Notice',
                'level' => 1,
                'description' => 'تنبيه إداري عام لمخالفة شروط الاستخدام',
                'duration_minutes' => null,
                'target_type' => 'all',
            ],

        ];

        foreach ($punishments as $punishment) {
            Punishment::create($punishment);
        }
    }
}