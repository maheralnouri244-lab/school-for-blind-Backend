<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AbsenceExcuse extends Model
{
    protected $guarded = [];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function caregiver()
    {
        return $this->belongsTo(Caregiver::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}