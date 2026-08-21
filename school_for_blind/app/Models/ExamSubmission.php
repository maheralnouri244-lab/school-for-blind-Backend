<?php
namespace App\Models;

use App\Models\Exam;
use App\Models\ExamStudentAnswer;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExamSubmission extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'student_id',
        'exam_id',
        'score',
        'status',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class, 'exam_id');
    }
    public function getAnswers()
    {
        return ExamStudentAnswer::where('exam_id', $this->exam_id)
            ->where('student_id', $this->student_id)
            ->get();
    }
}