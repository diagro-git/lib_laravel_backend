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
        logger()->debug("cache middleware called");
        $hasCacheHeaders = $request->hasHeader('x-diagro-cache-key') && $request->hasHeader('x-diagro-cache-tags');
        $shouldCacheResponse = $hasCacheHeaders;

        if($hasCacheHeaders) {
            logger()->debug("cache middleware called 2");
            CachedResource::$key = $request->hasHeader('x-diagro-cache-key');
            CachedResource::$tags = explode(' ', $request->hasHeader('x-diagro-cache-tags'));

            //is this a GET request and do we have a cache hit?
            $responseStatus = 200;
            $handler = API::getFailHandler();
            API::withFail(function($response) use(&$responseStatus) {
                $responseStatus = $response->status();
            });
            $data = API::backend((new Cache)->fetch());
            API::withFail($handler);

            //if status == OK, return data
            if($data != null && $responseStatus == 200) {
                $shouldCacheResponse = false;
                $response = response()->json($data);
            } else {
                $response = $next($request);
            }
        } else {
            $response = $next($request);
        }

        if($shouldCacheResponse) {
            CachedResource::cacheResponseAndResources($response->getData(true));
        }

        //delete the cached resource if needed
        CachedResource::deleteResources();

        return $response;
    }


}
