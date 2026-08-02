<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeacherAvailability extends Model
{
    protected $fillable = [
        'teacher_id',
        'day_of_week',
        'period_number',
    ];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }
}