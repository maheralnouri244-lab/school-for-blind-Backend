<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Jobs\RegradeQuizJob;
use App\Models\Quiz;
use App\Models\QuizSubmission;
use App\Models\StudentAnswer;
use App\Models\Subject;
use Illuminate\Http\Request;
use App\Models\Lesson;
use App\Models\Question;
use Illuminate\Support\Facades\DB;

class QuizWebController extends Controller
{
    public function index(Request $request)
    {
        $query = Quiz::with(['subject', 'lesson', 'teacher']);

        if ($request->filled('search')) {
            $query->whereHas('lesson', function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        $quizzes = $query->latest()->paginate(15);
        $subjects = Subject::all();

        return view('pages.quizzes.index', compact('quizzes', 'subjects'));
    }

    public function regrade($id)
    {
        $quiz = Quiz::findOrFail($id);
        RegradeQuizJob::dispatch($quiz->id);
        return back()->with('success', 'تم بدء عملية إعادة التصحيح لجميع الطلاب في الخلفية بنجاح! سيتم تحديث العلامات خلال لحظات.');
    }

    public function submissions($id, Request $request)
    {
        $quiz = Quiz::with('questions')->findOrFail($id);

        $query = QuizSubmission::with('student')
            ->where('quiz_id', $id);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $submissions = $query->latest()->paginate(15);

        $questionIds = $quiz->questions->pluck('id');

        foreach ($submissions as $submission) {
            $submission->answers = StudentAnswer::with(['question.choices', 'choice'])
                ->where('student_id', $submission->student_id)
                ->whereIn('question_id', $questionIds)
                ->get();
        }

        return view('pages.quizzes.submissions', compact('quiz', 'submissions'));
    }



    public function edit($id)
    {
        $quiz = Quiz::findOrFail($id);
        $subjects = Subject::all();
        $lessons = Lesson::where('subject_id', $quiz->subject_id)->get();

        return view('pages.quizzes.edit', compact('quiz', 'subjects', 'lessons'));
    }

    public function update(Request $request, $id)
    {
        $quiz = Quiz::findOrFail($id);

        $request->validate([
            'timelimit' => 'required|integer|min:1',
            'lesson_id' => 'required|exists:lessons,id',
        ]);

        $quiz->update([
            'timelimit' => $request->timelimit,
            'lesson_id' => $request->lesson_id,
        ]);

        return redirect()->route('dashboard.quizzes.show', $quiz->id)
            ->with('success', 'تم تحديث بيانات الكويز بنجاح!');
    }

    public function show($id)
    {
        $quiz = Quiz::with(['questions.choices', 'subject', 'lesson'])->findOrFail($id);

        return view('pages.quizzes.show', compact('quiz'));
    }

    public function destroy($id)
    {
        $quiz = Quiz::findOrFail($id);
        $quiz->delete();

        return redirect()->route('dashboard.quizzes.index')
            ->with('success', 'تم حذف الكويز بنجاح!');
    }

    public function storeQuestion(Request $request, $id)
    {
        $quiz = Quiz::findOrFail($id);

        $request->validate([
            'type' => 'required|in:mcq,TF,TEXT',
            'description' => 'required|string',
            'points' => 'required|numeric|min:0.5',
        ]);

        DB::beginTransaction();
        try {
            $question = Question::create([
                'teacher_id' => auth()->id() ?? 1,
                'type' => $request->type,
                'description' => $request->description,
                'points' => $request->points,
                'correct_answer' => $request->type !== 'mcq' ? $request->correct_answer : null,
                'status' => 'publish',
            ]);

            if ($request->type === 'mcq' && $request->has('choices')) {
                foreach ($request->choices as $index => $choice) {
                    if (!empty($choice['text'])) {
                        $question->choices()->create([
                            'choice_text' => $choice['text'],
                            'is_correct' => ($request->correct_choice == $index) ? true : false,
                        ]);
                    }
                }
            }

            $quiz->questions()->attach($question->id);
            $this->recalculateQuizTotals($quiz);
            DB::commit();
            return back()->with('success', 'تم إضافة السؤال للكويز بنجاح!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء إضافة السؤال: ' . $e->getMessage());
        }
    }

    public function detachQuestion($id, $question_id)
    {
        $quiz = Quiz::findOrFail($id);

        $quiz->questions()->detach($question_id);
        $this->recalculateQuizTotals($quiz);
        return back()->with('success', 'تمت إزالة السؤال من الكويز بنجاح!');
    }
    public function updateQuestionAnswer(Request $request, $id, $question_id)
    {
        $question = Question::findOrFail($question_id);
        $quiz = Quiz::findOrFail($id);

        if ($question->type === 'mcq') {
            $request->validate([
                'correct_choice' => 'required|exists:choices,id',
                'points' => 'required|numeric|min:0.5'
            ]);
            
            $question->choices()->update(['is_correct' => false]);
            $question->choices()->where('id', $request->correct_choice)->update(['is_correct' => true]);
            
            $question->update(['points' => $request->points]);
            
        } else {
            $request->validate([
                'correct_answer' => 'required|string',
                'points' => 'required|numeric|min:0.5'
            ]);
            
            $question->update([
                'correct_answer' => $request->correct_answer,
                'points' => $request->points
            ]);
        }

        $this->recalculateQuizTotals($quiz);

        return back()->with('success', 'تم تعديل السؤال وتحديث المجموع الكلي بنجاح!');
    }

    private function recalculateQuizTotals(\App\Models\Quiz $quiz)
    {
        $questionsCount = $quiz->questions()->count();
        $totalMark = $quiz->questions()->sum('points');

        $quiz->update([
            'numofquestions' => $questionsCount,
            'totalmark' => $totalMark,
        ]);
    }
}