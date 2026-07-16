<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bookmark extends Model
{
    protected $fillable = [
'name','timestamp_in_seconds',
'student_id',
'lesson_id'
    ];
public function student()
    {
        return $this->belongsTo(Student::class);
    }
   public function lesson()
    {
        return $this->belongsTo(Lesson::class); 
    }
   
}