<?php
namespace Diagro\Backend\Jobs;

use Diagro\Backend\Traits\CacheResourceHelpers;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Cache;

class CacheResources implements ShouldQueue
{

    use Dispatchable, InteractsWithQueue, Queueable, CacheResourceHelpers;


    public function __construct(
        private array $tags,
        private string $key,
        private array $usedResources
    )
    {
    }

    public function handle()
    {
        $value = ['key' => $this->key, 'tags' => $this->tags];

        foreach($this->usedResources as $usedResource) {
            $key = self::resourceKey($usedResource);
            $tags = [self::resourceTag($usedResource)];
            $cached = Cache::tags($tags)->get($key);

            if(empty($cached)) {
                $cached = [$key => [$value]];
            } elseif(is_array($cached)) {
                if(isset($cached[$key]) && is_array($cached[$key])) {
                    $cached[$key][] = $value;
                } else {
                    $cached[$key] = [$value];
                }
            }

            //tk = tags and keys
            Cache::tags($tags)->put('tk', $cached);
        }
    }

}