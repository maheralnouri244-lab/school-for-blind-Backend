<?php

namespace App\Events;

use App\Models\Exam;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ExamPublished
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $exam;

    public function __construct(Exam $exam)
    {
        $this->exam = $exam;
    }
}