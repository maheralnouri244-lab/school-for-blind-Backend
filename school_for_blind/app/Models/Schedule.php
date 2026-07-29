<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $fillable = [
        'class_id',
        'teacher_id',
        'subject_id',
        'day_of_week',
        'start_time',
        'end_time',
        'period_number',

    ];

   public function studentClass()
    {
        return $this->belongsTo(Classes::class, 'class_id', 'id');
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
    
}
