<?php

namespace App\Events;

use AllowDynamicProperties;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class VerificationCodeMailEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public User $user;
    public string $code;

    /**
     * @param User $user
     * @param string $code
     */
    public function __construct(User $user, string $code)
    {
        $this->user = $user;
        $this->code = $code;
        \Log::info('пришло в ивент', [
                'user' => $user,
                'code' => $code,
            ]
        );
    }


    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
