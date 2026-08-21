<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Choice extends Model
{
    use SoftDeletes;
    protected $fillable = ['question_id', 'choice_text', 'is_correct'];

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}