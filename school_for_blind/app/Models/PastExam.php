<?php

namespace App\Models;

use App\Models\Favorite;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PastExam extends Model
{
    protected $table = 'past_exams';

    protected $guarded = [];

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function questions(): BelongsToMany
    {
        return $this->belongsToMany(Question::class, 'past_exam_question', 'past_exam_id', 'question_id')
            ->withTimestamps();
    }
    public function favorites()
    {
        return $this->morphMany(Favorite::class, 'favorable');
    }
    protected static function booted()
    {
        static::addGlobalScope('examStats', function (Builder $builder) {
            $builder->withCount('questions as questionsCount')
                ->withSum('questions as totalmark', 'points');
        });
    }
}