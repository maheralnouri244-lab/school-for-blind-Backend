<?php

namespace App\Events;

use App\Models\QuizSubmission;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class QuizAutoGraded
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public QuizSubmission $submission;

    public function __construct(QuizSubmission $submission)
    {
        $this->submission = $submission;
    }
}