<?php
namespace Diagro\Backend\Traits;

use Illuminate\Support\Arr;

trait CacheResourceHelpers
{

    protected static function resourceTag(string $usedResource): string
    {
        return implode('.', explode('.', $usedResource, -1));
    }

    protected static function resourceKey(string $usedResource): string
    {
        return Arr::last(explode('.', $usedResource, 3));
    }

    protected static function resourceToCacheResourceKey(string $dbname, string $table, string $key): string
    {
        return sprintf('%s.%s.%s', $dbname, $table, $key);
    }

}