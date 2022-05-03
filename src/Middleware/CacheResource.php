<?php
namespace Diagro\Backend\Middleware;

use Closure;
use Diagro\API\API;
use Diagro\Backend\API\Cache;
use Diagro\Backend\Http\Resources\CachedResource;
use Illuminate\Http\Request;

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
        $hasCacheHeaders = $request->hasHeader('x-diagro-cache-key') && $request->hasHeader('x-diagro-cache-tags');
        if($hasCacheHeaders) {
            CachedResource::$key = $request->hasHeader('x-diagro-cache-key');
            CachedResource::$tags = explode(' ', $request->hasHeader('x-diagro-cache-tags'));
        }

        //is this a GET request and do we have a cache hit?
        $data = API::sync((new Cache)->fetch());
        if($data != null) {
            return response()->json($data);
        }

        $response = $next($request);

        if($hasCacheHeaders) {
            CachedResource::cacheResponseAndResources($response->getData(true));
        }

        //delete the cached resource if needed
        CachedResource::deleteResources();

        return $response;
    }


}
