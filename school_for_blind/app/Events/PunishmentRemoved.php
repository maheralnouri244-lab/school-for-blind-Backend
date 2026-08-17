<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PunishmentRemoved
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public string $punishmentName;

    public function __construct($user, string $punishmentName = 'العقوبة')
    {
        $this->user = $user;
        $this->punishmentName = $punishmentName;
    }
}