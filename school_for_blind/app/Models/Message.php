<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    // protected $fillable = ['conversation_id', 'body', 'attachment_path', 'attachment_type'];
    use SoftDeletes;

    protected $guarded = [];
    public function sender()
    {
        return $this->morphTo();
    }

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }
}