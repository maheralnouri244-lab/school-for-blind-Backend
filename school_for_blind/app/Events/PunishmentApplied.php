<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PunishmentApplied
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public string $reason;
    public ?string $expiresAt;

    public function __construct($user, string $reason, ?string $expiresAt = null)
    {
        $this->user = $user;
        $this->reason = $reason;
        $this->expiresAt = $expiresAt;
    }
}