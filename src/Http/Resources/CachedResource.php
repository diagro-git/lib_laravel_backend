<?php
namespace Diagro\Backend\Http\Resources;

use Diagro\API\API;
use Diagro\Backend\API\Cache;
use Illuminate\Database\Eloquent\Model;

class CachedResource
{


    public static array $tags = [];

    public static string $key = '';

    private static array $usedResources = [];

    private static array $deletedResources = [];


    public static function cacheResponseAndResources(array $data)
    {
        if(! empty(self::$tags) && ! empty(self::$key) && count(self::$usedResources) > 0) {
            API::backend((new Cache)->store($data, self::$usedResources));
        }
    }

    public static function deleteResources()
    {
        if(count(self::$deletedResources) > 0) {
            API::backend((new Cache)->delete(self::$deletedResources));
        }
    }

    public static function addUsedResource(string $dbname, string $tablename, string $key)
    {
        $resourceKey = self::resourceToCacheResourceKey($dbname, $tablename, $key);
        if(! in_array($resourceKey, self::$usedResources)) {
            self::$usedResources[] = $resourceKey;
        }
    }

    public static function addDeletedResource(string $dbname, string $tablename, string $key)
    {
        $resourceKey = self::resourceToCacheResourceKey($dbname, $tablename, $key);
        if(! in_array($resourceKey, self::$deletedResources)) {
            self::$deletedResources[] = $resourceKey;
        }
    }

    public static function addDeletedModel(Model $model)
    {
        self::addDeletedResource($model->getConnection()->getDatabaseName(), $model->getTable(), (string) $model->getKey());
    }

    public static function getUsedResources(): array
    {
        return self::$usedResources;
    }

    public static function getDeletedResource(): array
    {
        return self::$deletedResources;
    }


    protected static function resourceToCacheResourceKey(string $dbname, string $table, string $key): string
    {
        return sprintf('%s.%s.%s', $dbname, $table, $key);
    }
}