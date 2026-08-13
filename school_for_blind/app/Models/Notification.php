<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
'notifiable_type',
        'notifiable_id',
        'title',
        'body',
        'data',
        'read_at',
];
public function notifiable()
{
    return $this->morphTo();
}
}
