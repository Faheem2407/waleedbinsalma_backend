<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LatestMassageEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $senderId;
    public $receiverId;
    public $message;
    public $unreadMessageCount;

    /**
     * Create a new event instance.
     */
    public function __construct($senderId, $receiverId, $message, $unreadMessageCount)
    {
        $this->senderId = $senderId;
        $this->receiverId = $receiverId;
        $this->message = $message;
        $this->unreadMessageCount = $unreadMessageCount;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('latest-message-channel.' . $this->receiverId),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'senderId'   => $this->senderId,
            'receiverId' => $this->receiverId,
            'message' => $this->message,
            'unreadMessageCount' => $this->unreadMessageCount
        ];
    }
}
