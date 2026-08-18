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
}