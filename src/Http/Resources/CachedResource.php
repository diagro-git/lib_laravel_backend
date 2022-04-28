<?php
namespace Diagro\Backend\Http\Resources;

use Diagro\Backend\Jobs\CacheResources;
use Diagro\Backend\Traits\CacheResourceHelpers;
use LogicException;

class CachedResource
{

    use CacheResourceHelpers;


    public static array $tags = [];

    public static string $key = '';

    private static array $usedResources = [];


    /**
     * Start the job that links the used resources with tags and key.
     *
     * @return void
     */
    public static function cacheResources()
    {
        if(empty($tags) || empty($key)) {
            throw new LogicException("To cache resources you have to give me some tags and a key!");
        }

        CacheResources::dispatchAfterResponse(self::$tags, self::$key, self::getUsedResources());
    }

    public static function addUsedResource(string $dbname, string $tablename, string $key)
    {
        self::$usedResources[] = self::resourceToCacheResourceKey($dbname, $tablename, $key);
    }

    public static function getUsedResources(): array
    {
        return self::$usedResources;
    }

}