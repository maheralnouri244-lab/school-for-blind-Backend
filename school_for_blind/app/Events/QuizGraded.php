<?php

namespace App\Events;

use App\Models\QuizSubmission;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class QuizGraded
{
    use Dispatchable, SerializesModels;

    public $submission;

    public function __construct(QuizSubmission $submission)
    {
        $this->submission = $submission;
    }
}