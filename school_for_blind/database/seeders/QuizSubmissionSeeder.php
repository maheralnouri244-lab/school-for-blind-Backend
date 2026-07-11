<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Student;
use App\Models\Quiz;
use App\Models\StudentAnswer;
use App\Models\QuizSubmission;

class QuizSubmissionSeeder extends Seeder
{
    public function run(): void
    {
        $students = Student::take(3)->get();
        
        $quizzes = Quiz::with('questions.choices')->take(3)->get();

        if ($students->isEmpty() || $quizzes->isEmpty()) {
            $this->command->info('يرجى التأكد من وجود طلاب وكويزات في قاعدة البيانات أولاً.');
            return;
        }

        foreach ($quizzes as $quiz) {
            foreach ($students as $student) {
                $totalScore = 0;
                $hasUngradedText = false; 

                foreach ($quiz->questions as $question) {
                    $isCorrect = false;
                    $pointsEarned = 0;
                    $choiceId = null;
                    $textAnswer = null;
                    $isGraded = true;

                    if ($question->type === 'mcq') {
                        $correctChoice = $question->choices->where('is_correct', true)->first();
                        
                        if ($correctChoice) {
                            $choiceId = $correctChoice->id;
                            $isCorrect = true;
                            $pointsEarned = $question->points;
                            $totalScore += $pointsEarned;
                        }
                    } 
                    elseif ($question->type === 'TF') {
                        $textAnswer = $question->correct_answer; 
                        $isCorrect = true;
                        $pointsEarned = $question->points;
                        $totalScore += $pointsEarned;
                    } 
                    elseif ($question->type === 'TEXT') {
                        $textAnswer = 'هذه إجابة تجريبية من الطالب على السؤال النصي.';
                        $isCorrect = false; 
                        $pointsEarned = 0;
                        $isGraded = false; 
                        $hasUngradedText = true;
                    }

                    StudentAnswer::create([
                        'student_id' => $student->id,
                        'question_id' => $question->id,
                        'choice_id' => $choiceId,
                        'text_answer' => $textAnswer,
                        'is_correct' => $isCorrect,
                        'points_earned' => $pointsEarned,
                        'is_graded' => $isGraded,
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
}