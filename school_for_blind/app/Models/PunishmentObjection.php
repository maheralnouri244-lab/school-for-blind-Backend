<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PunishmentObjection extends Model
{
    protected $fillable = [
        'student_id',
        'caregiver_id',
        'punishable_record_id',
        'status',
        'reason',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
    public function caregiver()
    {
        return $this->belongsTo(Caregiver::class);
    }
    public function punishableRecord()
    {
        return $this->belongsTo(Punishable::class, 'punishable_record_id');
    }
}