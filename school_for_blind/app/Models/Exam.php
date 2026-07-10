<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Exam extends Model
{
    protected $table = 'exams';

    protected $guarded = [];

    protected $casts = [
        'exam_date' => 'datetime',
    ];

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function questions()
    {
        return $this->belongsToMany(Question::class, 'exam_question', 'exam_id', 'question_id')
                    ->withTimestamps();
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }

    public function submissions()
    {
        return $this->hasMany(ExamSubmission::class, 'exam_id');
    }
}