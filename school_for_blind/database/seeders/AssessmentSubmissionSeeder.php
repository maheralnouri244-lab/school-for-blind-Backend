<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Student;
use App\Models\Quiz;
use App\Models\Exam;
use App\Models\StudentAnswer;
use App\Models\QuizSubmission;
use App\Models\ExamSubmission;

class AssessmentSubmissionSeeder extends Seeder
{
    public function run(): void
    {
        $N = 15; 
        
        $studentsCount = Student::count();
        if ($studentsCount === 0) {
            $this->command->error('لا يوجد طلاب في قاعدة البيانات. يرجى إضافة طلاب أولاً.');
            return;
        }

        $limit = min($N, $studentsCount);

        $this->command->info("سيتم توليد {$limit} تقديم لكل تقييم (كويز / امتحان)...");

        $this->seedQuizSubmissions($limit);
        $this->seedExamSubmissions($limit);

        $this->command->info('تم توليد التقديمات بنجاح!');
    }

    private function seedQuizSubmissions($limit)
    {
        $quizzes = Quiz::with('questions.choices')->get(); 

        foreach ($quizzes as $quiz) {
            $students = Student::inRandomOrder()->take($limit)->get();

            foreach ($students as $student) {
                $totalScore = 0;
                $hasUngradedText = false;

                foreach ($quiz->questions as $question) {
                    $isCorrect = false;
                    $pointsEarned = 0;
                    $choiceId = null;
                    $textAnswer = null;
                    $isGraded = true;
                    $audioAnswer = null;

                    $studentKnowsAnswer = rand(1, 100) <= 60;

                    if ($question->type === 'mcq') {
                        if ($studentKnowsAnswer) {
                            $choice = $question->choices->where('is_correct', true)->first();
                        } else {
                            $choice = $question->choices->where('is_correct', false)->random() ?? $question->choices->first();
                        }

                        if ($choice) {
                            $choiceId = $choice->id;
                            $isCorrect = $choice->is_correct;
                            $pointsEarned = $isCorrect ? $question->points : 0;
                            $totalScore += $pointsEarned;
                        }
                    } 
                    elseif ($question->type === 'TF') {
                        $isCorrect = $studentKnowsAnswer;
                        $textAnswer = $isCorrect ? $question->correct_answer : ($question->correct_answer == 'true' ? 'false' : 'true'); 
                        $pointsEarned = $isCorrect ? $question->points : 0;
                        $totalScore += $pointsEarned;
                    } 
                    elseif ($question->type === 'TEXT') {
                        if (rand(0, 1) == 1) {
                            $textAnswer = 'هذه إجابة نصية تجريبية من الطالب على السؤال المقالي.';
                        } else {
                            $audioAnswer = 'student_audios/fake_record_sample.wav'; 
                        }

                        $isTeacherGradedIt = rand(1, 100) <= 70; 
                        
                        if ($isTeacherGradedIt) {
                            $isGraded = true;
                            $isCorrect = $studentKnowsAnswer;
                            $pointsEarned = $isCorrect ? $question->points :
                             (rand(0, $question->points - 1));
                            $totalScore += $pointsEarned;
                        } else {
                            $isGraded = false;
                            $hasUngradedText = true;
                        }
                    }

                    StudentAnswer::create([
                        'student_id' => $student->id,
                        'question_id' => $question->id,
                        'choice_id' => $choiceId,
                        'text_answer' => $textAnswer,
                        'is_correct' => $isCorrect,
                        'points_earned' => $pointsEarned,
                        'is_graded' => $isGraded,
                        'audio_answer'  => $audioAnswer, 
                    ]);
                }

                QuizSubmission::create([
                    'student_id' => $student->id,
                    'quiz_id' => $quiz->id,
                    'total_score' => $totalScore,
                    'status' => $hasUngradedText ? 'pending' : 'graded',
                    'teacher_assigned_mark' => 0,
                ]);
            }
        }
    }

    private function seedExamSubmissions($limit)
    {
        $exams = Exam::all();

        $examStatuses = ['pending_grading', 'pending_approval', 'approved', 'rejected'];

        foreach ($exams as $exam) {
            $students = Student::inRandomOrder()->take($limit)->get();

            foreach ($students as $student) {
                $status = rand(1, 100) <= 70 ? 'approved' : $examStatuses[array_rand($examStatuses)];
                $score = $status === 'approved' ? (rand(10, $exam->totalmark * 10) / 10) : 0; 

                ExamSubmission::create([
                    'student_id' => $student->id,
                    'exam_id' => $exam->id,
                    'score' => $score,
                    'status' => $status,
                ]);
            }
        }
    }
}