<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Teacher;
use App\Models\Subject;
use App\Models\Student;
use App\Models\Question;
use App\Models\Choice;
use App\Models\Exam;
use App\Models\ExamSubmission;
use App\Models\ExamStudentAnswer;

class ExamSeeder extends Seeder
{
    public function run(): void
    {
        $teacher = Teacher::where('phone', '0943576695')->first();

        $subjects = Subject::take(2)->get();
        $students = Student::take(3)->get();

        if (!$teacher || $subjects->isEmpty() || $students->isEmpty()) {
            $this->command->info('تأكد من تشغيل سيدرات المعلمين والمواد والطلاب أولاً.');
            return;
        }

        $mcqQuestion = Question::create([
            'teacher_id' => $teacher->id,
            'type' => 'mcq',
            'description' => 'ما هي عاصمة سوريا؟',
            'points' => 2,
            'status' => 'publish',
        ]);
        $correctChoice = Choice::create(['question_id' => $mcqQuestion->id, 'choice_text' => 'دمشق', 'is_correct' => true]);
        Choice::create(['question_id' => $mcqQuestion->id, 'choice_text' => 'حلب', 'is_correct' => false]);
        Choice::create(['question_id' => $mcqQuestion->id, 'choice_text' => 'حمص', 'is_correct' => false]);

        $tfQuestion = Question::create([
            'teacher_id' => $teacher->id,
            'type' => 'TF',
            'description' => 'المربع يحتوي على 4 أضلاع متساوية.',
            'correct_answer' => 'true',
            'points' => 1,
            'status' => 'publish',
        ]);

        $textQuestion = Question::create([
            'teacher_id' => $teacher->id,
            'type' => 'TEXT',
            'description' => 'اشرح باختصار أسباب الثورة الصناعية.',
            'correct_answer' => 'إجابة تعتمد على الفهم.',
            'points' => 5,
            'status' => 'publish',
        ]);

        foreach ($subjects as $subject) {
            $exam = Exam::create([
                'teacher_id' => $teacher->id,
                'title' => 'امتحان تجريبي - ' . $subject->name,
                'description' => 'امتحان شامل لاختبار قدرات الطلاب',
                'subject_id' => $subject->id,
                'duration_minutes' => 60,
                'is_published' => true,
                'numofquestions' => 3,
                'totalmark' => 8,
            ]);

            $exam->questions()->attach([$mcqQuestion->id, $tfQuestion->id, $textQuestion->id]);

            foreach ($students as $student) {
                $submission = ExamSubmission::create([
                    'student_id' => $student->id,
                    'exam_id' => $exam->id,
                    'score' => 3,
                    'status' => 'pending_grading',
                ]);

                ExamStudentAnswer::create([
                    'student_id' => $student->id,
                    'exam_id' => $exam->id,
                    'question_id' => $mcqQuestion->id,
                    'choice_id' => $correctChoice->id,
                    'is_correct' => true,
                    'points_earned' => 2,
                    'is_graded' => true,
                ]);

                ExamStudentAnswer::create([
                    'student_id' => $student->id,
                    'exam_id' => $exam->id,
                    'question_id' => $tfQuestion->id,
                    'text_answer' => 'true',
                    'is_correct' => true,
                    'points_earned' => 1,
                    'is_graded' => true,
                ]);

                ExamStudentAnswer::create([
                    'student_id' => $student->id,
                    'exam_id' => $exam->id,
                    'question_id' => $textQuestion->id,
                    'text_answer' => 'الثورة الصناعية بدأت بسبب الحاجة لزيادة الإنتاج واستخدام الآلات البخارية...',
                    'is_correct' => false,
                    'points_earned' => 0,
                    'is_graded' => false,
                ]);
            }
        }
    }
}