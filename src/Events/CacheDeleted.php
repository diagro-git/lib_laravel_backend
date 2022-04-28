<?php
namespace Diagro\Backend\Events;

use Exception;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;
use Pusher\Pusher;

class CacheDeleted implements ShouldBroadcast
{

    use SerializesModels;


    public $queue = 'events_cache';


    public function __construct(private string $key, public array $tags, private int $user_id, private int $company_id)
    {
    }

    public function broadcastAs(): string
    {
        return "deleted";
    }

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('Diagro.Cache.' . $this->user_id . '.' . $this->company_id);
    }

    public function broadcastWhen(): bool
    {
        try {
            $pusher = new Pusher(env('PUSHER_APP_KEY'), env('PUSHER_APP_SECRET'), env('PUSHER_APP_ID'), ['cluster' => env('PUSHER_APP_CLUSTER')]);
            $info = $pusher->getChannelInfo('private-Diagro.Cache.' . $this->user_id . '.' . $this->company_id);
            return $info->occupied;
        } catch(Exception $e)
        {
            return false;
        }
    }

}
