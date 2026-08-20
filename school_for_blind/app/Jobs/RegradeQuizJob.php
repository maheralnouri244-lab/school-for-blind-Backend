<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\QuizSubmission;
use App\Models\StudentAnswer;
use App\Models\Question;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RegradeQuizJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $quizId;

    /**
     * Create a new job instance.
     */
    public function __construct($quizId)
    {
        $this->quizId = $quizId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            DB::beginTransaction();

            $submissions = QuizSubmission::where('quiz_id', $this->quizId)->get();

            $quizQuestions = Question::whereHas('quizzes', function ($q) {
                $q->where('quizzes.id', $this->quizId);
            })->with('choices')->get()->keyBy('id');

            foreach ($submissions as $submission) {
                $studentId = $submission->student_id;

                $answers = StudentAnswer::where('student_id', $studentId)
                    ->whereIn('question_id', $quizQuestions->keys())
                    ->get();

                $newTotalScore = 0;

                foreach ($answers as $answer) {
                    $question = $quizQuestions->get($answer->question_id);

                    if (!$question)
                        continue;

                    if ($question->type === 'TEXT') {
                        $newTotalScore += (float) $answer->points_earned;
                        continue;
                    }

                    $isCorrect = false;
                    $pointsEarned = 0;

                    if ($question->type === 'mcq') {
                        if ($answer->choice_id) {
                            $choice = $question->choices->where('id', $answer->choice_id)->first();
                            if ($choice && $choice->is_correct) {
                                $isCorrect = true;
                                $pointsEarned = (float) $question->points;
                            }
                        }
                    }
                    elseif ($question->type === 'TF') {
                        if ($answer->text_answer && strcasecmp(trim($answer->text_answer), trim($question->correct_answer)) === 0) {
                            $isCorrect = true;
                            $pointsEarned = (float) $question->points;
                        }
                    }

                    // تحديث الإجابة بجدول student_answers
                    $answer->update([
                        'is_correct' => $isCorrect,
                        'points_earned' => $pointsEarned
                    ]);

                    // إضافة العلامة للمجموع الجديد
                    $newTotalScore += $pointsEarned;
                }

                // 5. تحديث المجموع النهائي للطالب بجدول quiz_submissions
                $submission->update([
                    'total_score' => $newTotalScore
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('خطأ أثناء إعادة تصحيح الكويز رقم ' . $this->quizId . ': ' . $e->getMessage());
        }
    }
}