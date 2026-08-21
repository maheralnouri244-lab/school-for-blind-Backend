<?php

namespace App\Events;

use App\Models\Student;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ParentReportGenerated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Student $student;
    public string $reportType; 
    public string $referenceDate;

    public function __construct(Student $student, string $reportType, string $referenceDate)
    {
        $this->student = $student;
        $this->reportType = $reportType;
        $this->referenceDate = $referenceDate;
    }
}