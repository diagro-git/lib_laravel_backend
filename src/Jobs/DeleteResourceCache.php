<?php
namespace Diagro\Backend\Jobs;

use Diagro\Backend\Events\CacheDeleted;
use Diagro\Backend\Traits\CacheResourceHelpers;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;

class DeleteResourceCache implements ShouldQueue
{

    use Dispatchable, InteractsWithQueue, Queueable, CacheResourceHelpers;


    private array $event = [];


    public function __construct(
        private string $resource
    )
    {
    }

    public function middleware(): array
    {
        return [new WithoutOverlapping($this->resourceKey($this->resource))];
    }

    public function handle()
    {
        $key = self::resourceKey($this->resource);
        $tag = self::resourceTag($this->resource);
        $cache = Cache::tags([$tag]);
        $entries = $cache->get('tk'); //tags and keys

        if(empty($entries) || ! is_array($entries)) return;

        if($key == '*') {
            foreach($entries as $tags_keys) {
                if(is_array($tags_keys)) {
                    $this->handleTagsKeys($tags_keys);
                }
            }

            $cache->flush(); //delete references
        } elseif(isset($entries[$key])) {
            $tags_keys = $entries[$key];
            if(is_array($tags_keys)) {
                $this->handleTagsKeys($tags_keys);
            }

            //remove reference and update cache references
            unset($entries[$key]);
            $cache->put('tk', $entries);
        }

        $this->sendEvents();
    }

    private function handleTagsKeys(array $tags_keys)
    {
        foreach($tags_keys as $tags_key) {
            if(is_array($tags_key) && $this->validateTagKey($tags_key)) {
                Cache::tags($tags_key['tags'])->forget($tags_key['key']);
                $this->event($tags_key);
            }
        }
    }

    private function event(array $tags_key)
    {
        $user_id = null;
        $tags = $tags_key['tags'];
        $key = $tags_key['key'];

        foreach($tags as $tag) {
            if(str_starts_with($tag, 'user_')) {
                $user_id = Arr::last(explode('_', $tag));
            }
        }

        //send event if company and user is not null.
        if($user_id != null) {
            if(! isset($this->event[$user_id])) {
                $this->event[$user_id] = [];
            }
            $this->event[$user_id][] = $tags_key;
        }
    }

    private function sendEvents()
    {
        foreach($this->event as $userId => $keys_tags) {
            event(new CacheDeleted($keys_tags, $userId));
        }
    }

    private function validateTagKey(array $tag_key): bool
    {
        return isset($tag_key['tags']) && isset($tag_key['key']);
    }

}