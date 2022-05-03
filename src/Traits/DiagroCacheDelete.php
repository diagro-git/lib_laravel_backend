<?php
namespace Diagro\Backend\Traits;

use Diagro\Backend\Http\Resources\CachedResource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
            CachedResource::addDeletedResource($dbname, $table, '*');
        });

        static::updated(function(Model $model) {
            $dbname = $model->getConnection()->getDatabaseName();
            $table = $model->getTable();
            $key = $model->getKey();
            if(is_array($key)) {
                $key = implode('.', $key);
            } elseif (! is_string($key)) {
                $key = (string)$key;
            }
            CachedResource::addDeletedResource($dbname, $table, $key);
        });

        static::deleted(function(Model $model) {
            $dbname = $model->getConnection()->getDatabaseName();
            $table = $model->getTable();
            $key = $model->getKey();
            if(is_array($key)) {
                $key = implode('.', $key);
            } elseif (! is_string($key)) {
                $key = (string)$key;
            }
            CachedResource::addDeletedResource($dbname, $table, $key);
        });

        if(in_array(SoftDeletes::class, class_uses_recursive(static::class))) {
            static::restored(function (Model $model) {
                $dbname = $model->getConnection()->getDatabaseName();
                $table = $model->getTable();
                $key = $model->getKey();
                if (is_array($key)) {
                    $key = implode('.', $key);
                } elseif (!is_string($key)) {
                    $key = (string)$key;
                }
                CachedResource::addDeletedResource($dbname, $table, $key);
            });
        }
    }


}