<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuizSubmission extends Model
{
    use SoftDeletes;
    protected $fillable = ['student_id', 'quiz_id', 'teacher_assigned_mark', 'total_score', 'status'];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }
}