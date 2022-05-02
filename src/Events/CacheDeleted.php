<?php
namespace Diagro\Backend\Events;

use Diagro\Events\BroadcastWhenOccupied;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class CacheDeleted implements ShouldBroadcast
{

    use SerializesModels, BroadcastWhenOccupied;



    public function __construct(public string $key, public array $tags, $user_id)
    {
        $this->user_id = $user_id;
    }

    public function broadcastAs(): string
    {
        return "deleted";
    }

    protected function channelName(): string
    {
        return "Diagro.Cache";
    }
}
