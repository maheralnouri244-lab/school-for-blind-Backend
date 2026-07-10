<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamStudentAnswer extends Model
{
    protected $guarded = [];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id'); 
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class, 'exam_id');
    }

    public function question()
    {
        return $this->belongsTo(Question::class, 'question_id');
    }

    public function choice()
    {
        return $this->belongsTo(Choice::class, 'choice_id');
    }
    
}