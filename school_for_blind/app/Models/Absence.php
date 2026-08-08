<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absence extends Model
{
    use HasFactory;

    protected $fillable = ['student_id', 'room_id', 'date'];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function excuse()
    {
        return $this->hasOne(AbsenceExcuse::class, 'room_id', 'room_id');
    }
}