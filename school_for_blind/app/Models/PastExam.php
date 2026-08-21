<?php

namespace App\Models;

use App\Models\Favorite;
use App\Models\Question;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PastExam extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'past_exams';

    protected $guarded = [];

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function questions()
    {
        return $this->belongsToMany(Question::class, 'past_exam_question')
            ->withTimestamps()
            ->withTrashed();
    }
    public function favorites()
    {
        return $this->morphMany(Favorite::class, 'favorable');
    }
    // protected static function booted()
    // {
    //     static::addGlobalScope('examStats', function (Builder $builder) {
    //         $builder->withCount('questions as questionsCount')
    //             ->withSum('questions as totalmark', 'points');
    //     });
    // }
}