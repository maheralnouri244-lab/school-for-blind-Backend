<?php

namespace App\Models;

use App\Models\ExamSubmission;
use App\Models\Favorite;
use App\Models\Question;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Exam extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'exams';

    protected $guarded = [];

    protected $casts = [
        'exam_date' => 'datetime',
        'is_published' => 'boolean',
    ];

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id')->withTrashed();
    }

    public function questions()
    {
        return $this->belongsToMany(Question::class, 'exam_question', 'exam_id', 'question_id')
            ->withTimestamps()
            ->with('choices')
            ->withTrashed();
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacher_id')->withTrashed();
    }

    public function submissions()
    {
        return $this->hasMany(ExamSubmission::class, 'exam_id');
    }
    public function favorites()
    {
        return $this->morphMany(Favorite::class, 'favorable');
    }
}