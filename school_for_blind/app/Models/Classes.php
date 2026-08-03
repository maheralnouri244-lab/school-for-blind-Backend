<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Classes extends Model
{
    protected $table = 'classes';
    protected $fillable = [
        'name',
        'level',
        'number',
    ];

    public function students()
    {
        return $this->hasMany(Student::class, 'class_id');
    }


    public function teachers()
    {
        return $this->belongsToMany(Teacher::class, 'class_teacher', 'class_id', 'teacher_id');
    }

    public function rooms()
    {
        return $this->hasMany(Room::class, 'class_id');
    }
    public function lessons()
    {
        return $this->hasMany(Lesson::class, 'class_id');
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class, 'class_id');
    }
}
