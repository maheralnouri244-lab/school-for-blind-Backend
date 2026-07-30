<?php

namespace App\Events;

use App\Models\Quiz;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class QuizCreated implements ShouldBroadcast 
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $quiz;
    public $class_id; 

    public function __construct(Quiz $quiz, $class_id)
    {
        $this->quiz = $quiz;
        $this->class_id = $class_id;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel("quizzes.class.{$this->class_id}")
        ];
    }

    public function broadcastAs(): string
    {
        return 'new-quiz';
    }

    public function broadcastWith(): array
    {
        return [
            'id'             => $this->quiz->id,
            'subject_name'   => $this->quiz->subject_name,
            'lesson_id'      => $this->quiz->lesson_id,
            'numofquestions' => $this->quiz->numofquestions,
            'timelimit'      => $this->quiz->timelimit,
            'created_at'     => $this->quiz->created_at->toDateTimeString(),
        ];
    }
}