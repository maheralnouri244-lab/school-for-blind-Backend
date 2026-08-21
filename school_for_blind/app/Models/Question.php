<?php

namespace App\Models;

use App\Models\Choice;
use App\Models\Exam;
use App\Models\PastExam;
use App\Models\Quiz;
use App\Models\Teacher;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Question extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = [];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }

    public function quizzes()
    {
        return $this->belongsToMany(Quiz::class);
    }

    public function choices()
    {
        return $this->hasMany(Choice::class);
    }

    public function pastExams(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(PastExam::class, 'past_exam_question', 'question_id', 'past_exam_id')
            ->withTimestamps();
    }

    public function exams(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Exam::class, 'exam_question', 'question_id', 'exam_id')
            ->withTimestamps();
    }
}
