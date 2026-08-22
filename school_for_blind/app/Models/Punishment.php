<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Punishment extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'name',
        'description',
        'level',
        'duration',
        'target_type',
    ];
}
