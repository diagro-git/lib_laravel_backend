<?php
namespace Diagro\Backend\Middleware;

use Closure;
use Diagro\Backend\Http\Resources\CachedResource;
use Diagro\Backend\Jobs\CacheResources;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

/**
 * Checks if the X-DIAGRO-CACHE header is precense.
 * Extract the key and tags from the header and set it to the CachedResource class.
 *
 * @package Diagro\Backend\Middleware
 */
class CacheResource
{


    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if($request->hasHeader('x-diagro-cache')) {
            $tags_key = explode(';', $request->header('x-diagro-cache'));
            CachedResource::$key = $tags_key[0];
            CachedResource::$tags = explode(' ', $tags_key[1]);
        }

        $response = $next($request);

        if($request->hasHeader('x-diagro-cache')) {
            logger()->debug($request->header('x-diagro-cache'));
            CachedResource::cacheResources();
        }

        return $response;
    }


}
