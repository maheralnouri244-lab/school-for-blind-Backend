<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupportTicket extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    public function sender()
    {
        return $this->morphTo();
    }

    public function classifier()
    {
        return $this->belongsTo(Admin::class, 'classified_by');
    }
}