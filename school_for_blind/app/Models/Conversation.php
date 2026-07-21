<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    // protected $fillable = ['type', 'name', 'teacher_id', 'subject_id', 'parent_id'];

    protected $guarded = [];
    
    public function discussion()
    {
        return $this->hasOne(Conversation::class, 'parent_id')->where('type', 'discussion');
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
    public function parent()
    {
        return $this->belongsTo(Conversation::class, 'parent_id');
    }
    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }
}