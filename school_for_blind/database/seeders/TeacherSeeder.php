<?php

namespace Database\Seeders;

use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class TeacherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('ar_SA');

        $classesMap = DB::table('classes')->pluck('name', 'id')->toArray();

        // جلب الـ IDs تبع الشعب وفصلهم حسب الصف
        $ninthClasses = DB::table('classes')->where('level', 'ninth')->pluck('id')->toArray();
        $twelfthClasses = DB::table('classes')->where('level', 'twelfth')->pluck('id')->toArray();

        // جلب الـ IDs تبع المواد وفصلهم حسب الصف للأساتذة العشوائيين
        $ninthSubjects = DB::table('subjects')->where('grade_level', 'ninth')->pluck('id')->toArray();
        $twelfthSubjects = DB::table('subjects')->where('grade_level', 'twelfth')->pluck('id')->toArray();
        $availableSubjectsAll = DB::table('subjects')->pluck('id')->toArray();

        $commonPassword = Hash::make('12345678');

        // ==========================================
        // القسم الأول: الأساتذة المعروفين مسبقاً
        // ==========================================
        $famousTeachers = [
            // === أساتذة البكالوريا (twelfth) ===
            ['name' => 'الأستاذ أحمد حيدر', 'subject' => 'الفلسفة', 'level' => 'twelfth', 'phone' => '0911111111'],
            ['name' => 'الأستاذ نضال يوسف', 'subject' => 'التاريخ', 'level' => 'twelfth', 'phone' => '0922222222'],
            ['name' => 'الأستاذ موسى الرز', 'subject' => 'الجغرافيا', 'level' => 'twelfth', 'phone' => '0933333333'],
            ['name' => 'الأستاذ مصطفى الشيخ أحمد', 'subject' => 'اللغة العربية', 'level' => 'twelfth', 'phone' => '0944444444'],
            ['name' => 'الأستاذ أنس أحمد', 'subject' => 'اللغة الإنكليزية', 'level' => 'twelfth', 'phone' => '0955555555'],
            ['name' => 'الأستاذ يامن قيس', 'subject' => 'اللغة الفرنسية', 'level' => 'twelfth', 'phone' => '0966666666'],
            ['name' => 'الشيخ عبد الله علوش', 'subject' => 'التربية الدينية', 'level' => 'twelfth', 'phone' => '0977777777'],

            // === أساتذة التاسع (ninth) ===
            ['name' => 'الأستاذ فايز جوهرة', 'subject' => 'الرياضيات (جبر)', 'level' => 'ninth', 'phone' => '0988888888'],
            ['name' => 'الأستاذ محمد نور طحّان', 'subject' => 'الفيزياء والكيمياء', 'level' => 'ninth', 'phone' => '0999999999'],
            ['name' => 'الأستاذة رنا خير بك', 'subject' => 'علم الأحياء والأرض', 'level' => 'ninth', 'phone' => '0912345678'],
            ['name' => 'الأستاذ علاء العبيد', 'subject' => 'التاريخ', 'level' => 'ninth', 'phone' => '0923456789'],
            ['name' => 'الأستاذ حيان حيدر', 'subject' => 'الجغرافيا', 'level' => 'ninth', 'phone' => '0934567890'],
            ['name' => 'الأستاذ طارق فرزات', 'subject' => 'اللغة العربية', 'level' => 'ninth', 'phone' => '0945678901'],
            ['name' => 'الأستاذ عمار ملوحي', 'subject' => 'اللغة الإنكليزية', 'level' => 'ninth', 'phone' => '0956789012'],
            ['name' => 'الأستاذ باسل زعرور', 'subject' => 'اللغة الفرنسية', 'level' => 'ninth', 'phone' => '0967890123'],
            ['name' => 'الآنسة هدى الموصلي', 'subject' => 'التربية الدينية', 'level' => 'ninth', 'phone' => '0978901234'],
        ];

        $consoleData = [];

        foreach ($famousTeachers as $tData) {
            $level = $tData['level'];
            $availableClasses = ($level === 'ninth') ? $ninthClasses : $twelfthClasses;
            $randomDate = $faker->dateTimeBetween('-11 months', 'now');

            $dbSubject = Subject::where('name', $tData['subject'])
                ->where('grade_level', $tData['level'])
                ->first();

            $teacher = Teacher::updateOrCreate(
                ['phone' => $tData['phone']],
                [
                    'full_name' => $tData['name'],
                    'password' => $commonPassword,
                    'level' => $level,
                    'status' => 'approved',
                    'cv_path' => 'cv_dummy.pdf',
                    'created_at' => $randomDate,
                    'updated_at' => $randomDate,
                ]
            );

            // ربط الشعب
            $assignedClassesIds = [];
            if (!empty($availableClasses)) {
                $classesCountToAssign = min(rand(1, 2), count($availableClasses));
                $keys = (array) array_rand($availableClasses, $classesCountToAssign);
                foreach ($keys as $key) {
                    $assignedClassesIds[] = $availableClasses[$key];
                }
                $teacher->classes()->sync($assignedClassesIds);
            }

            // ربط المواد
            $assignedSubjectIds = [];
            if ($dbSubject) {
                $assignedSubjectIds[] = $dbSubject->id;
            } else {
                $fallbackSubject = Subject::where('grade_level', $level)->first();
                if ($fallbackSubject) {
                    $assignedSubjectIds[] = $fallbackSubject->id;
                } elseif (!empty($availableSubjectsAll)) {
                    $assignedSubjectIds[] = $availableSubjectsAll[array_rand($availableSubjectsAll)];
                }
            }
            $teacher->subjects()->sync($assignedSubjectIds);

            // تجهيز البيانات لطباعتها في الكونسول
            $assignedClassesNames = array_map(function ($id) use ($classesMap) {
                return $classesMap[$id] ?? "شعبة ($id)";
            }, $assignedClassesIds);

            $assignedSubjectsNames = $teacher->subjects()->pluck('name')->toArray();
            if (empty($assignedSubjectsNames)) {
                $assignedSubjectsNames = [$tData['subject']];
            }

            $token = $teacher->createToken('teacher-test-token')->plainTextToken;

            $consoleData[] = [
                $teacher->full_name,
                $teacher->phone,
                $level,
                implode(', ', $assignedSubjectsNames),
                implode(', ', $assignedClassesNames),
                $token,
            ];
        }

        $this->command->info('=== Famous Teachers & Their Assigned Subjects/Classes ===');
        $this->command->table(
            ['Teacher Name', 'Phone', 'Level', 'Assigned Subjects', 'Assigned Classes', 'Token'],
            $consoleData
        );


        // ==========================================
        // القسم الثاني: 60 أستاذ جديد بمواصفاتك
        // ==========================================
        for ($i = 0; $i < 60; $i++) {
            $randomDate = $faker->dateTimeBetween('-11 months', 'now');
            $level = ['ninth', 'twelfth'][array_rand(['ninth', 'twelfth'])];

            $availableClasses = ($level === 'ninth') ? $ninthClasses : $twelfthClasses;
            $availableSubjects = ($level === 'ninth') ? $ninthSubjects : $twelfthSubjects;

            $teacher = Teacher::create([
                'full_name' => $faker->name,
                'phone' => '09' . $faker->unique()->randomNumber(8, true),
                'password' => $commonPassword,
                'level' => $level,
                'status' => 'approved',
                'cv_path' => 'random_cv.pdf',
                'created_at' => $randomDate,
                'updated_at' => $randomDate,
            ]);

            // إسناد من مادتين لـ 3 مواد حصراً
            if (!empty($availableSubjects)) {
                $subjectsCount = rand(2, 3);
                $subjectsCount = min($subjectsCount, count($availableSubjects));

                $subjectKeys = (array) array_rand($availableSubjects, $subjectsCount);
                $assignedSubjectIds = array_map(function ($key) use ($availableSubjects) {
                    return $availableSubjects[$key];
                }, $subjectKeys);

                $teacher->subjects()->sync($assignedSubjectIds);
            }

            // إسناد من 4 لـ 5 شعب حصراً
            if (!empty($availableClasses)) {
                $classesCount = rand(4, 5);
                $classesCount = min($classesCount, count($availableClasses));

                $classKeys = (array) array_rand($availableClasses, $classesCount);
                $assignedClassIds = array_map(function ($key) use ($availableClasses) {
                    return $availableClasses[$key];
                }, $classKeys);

                $teacher->classes()->sync($assignedClassIds);
            }
        }

        $this->command->info('✅ تم توليد 60 أستاذ جديد بنجاح وربطهم بشعب ومواد صفهم حصراً!');
    }
}