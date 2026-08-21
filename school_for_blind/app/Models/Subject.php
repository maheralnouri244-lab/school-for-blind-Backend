<?php

namespace App\Models;

use App\Models\Quiz;
use App\Models\Teacher;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subject extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = [];
    protected $appends = ['full_name'];
    public function quizzes()
    {
        return $this->hasMany(Quiz::class);
    }
    public function teachers()
    {
        return $this->belongsToMany(Teacher::class, 'teacher_subjects', 'subject_id', 'teacher_id');
    }

    public function lessons()
    {
        return $this->hasMany(Lesson::class);
    }

    public function getFullNameAttribute()
    {
        if ($this->grade_level) {
            return $this->name . ' - ' . ($this->grade_level == 'ninth' ? 'التاسع' : 'الثاني عشر');
        }
        return $this->name;
    }

    protected static function booted(): void
    {
        static::deleting(function (Subject $subject) {
            if (!$subject->isForceDeleting()) {
                $subject->lessons()->delete();
                $subject->quizzes()->delete();
                $subject->exams()->delete();
                $subject->pastExams()->delete();
            }
        });

        static::restoring(function (Subject $subject) {
            $subject->lessons()->restore();
            $subject->quizzes()->restore();
            $subject->exams()->restore();
            $subject->pastExams()->restore();
        });
    }

    public function exams()
    {
        return $this->hasMany(Exam::class)->withTrashed();
    }

    public function pastExams()
    {
        return $this->hasMany(PastExam::class)->withTrashed();
    }

}