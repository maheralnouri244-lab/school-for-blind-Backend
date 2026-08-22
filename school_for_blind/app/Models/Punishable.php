<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\MorphPivot;

class Punishable extends MorphPivot
{
    protected $table = 'punishables';

    public $incrementing = true;

    protected $fillable = [
        'punishment_id',
        'punishable_id',
        'punishable_type',
        'user_phone',
        'admin_id',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function punishment()
    {
        return $this->belongsTo(Punishment::class);
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    public function punishable()
    {
        return $this->morphTo();
    }
}