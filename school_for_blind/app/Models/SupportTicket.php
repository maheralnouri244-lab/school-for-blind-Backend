<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupportTicket extends Model
{
    use SoftDeletes;
protected $fillable = [
        'sender_id',
        'sender_type',
        'message',
        'attachment_path',
        'priority',
        'status',
        'assigned_department',
        'classified_by',
    ];
    protected $casts = [
        'attachment_path' => 'array',
    ];
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