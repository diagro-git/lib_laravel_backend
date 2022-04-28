<?php
namespace Diagro\Backend\Traits;

use Diagro\Backend\Jobs\DeleteResourceCache;
use Illuminate\Database\Eloquent\Model;

/**
 * When a model is created, deleted or updated. The cache entries should be deleted.
 *
 * @package Diagro\Backend\Traits
 */
trait DiagroCacheDelete
{

    use CacheResourceHelpers;


    protected static function bootDiagroCacheDelete()
    {
        static::created(function(Model $model) {
            $dbname = $model->getConnection()->getDatabaseName();
            $table = $model->getTable();
            DeleteResourceCache::dispatchAfterResponse(self::resourceToCacheResourceKey($dbname, $table, '*'));
        });

        static::updated(function(Model $model) {
            $dbname = $model->getConnection()->getDatabaseName();
            $table = $model->getTable();
            $key = $model->getKey();
            if(is_array($key)) {
                $key = implode('.', $key);
            }
            DeleteResourceCache::dispatchAfterResponse(self::resourceToCacheResourceKey($dbname, $table, $key));
        });

        static::deleted(function(Model $model) {
            $dbname = $model->getConnection()->getDatabaseName();
            $table = $model->getTable();
            $key = $model->getKey();
            if(is_array($key)) {
                $key = implode('.', $key);
            }
            DeleteResourceCache::dispatchAfterResponse(self::resourceToCacheResourceKey($dbname, $table, $key));
        });
    }


}