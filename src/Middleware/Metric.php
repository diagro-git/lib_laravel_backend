<?php
namespace Diagro\Backend\Middleware;

use Closure;
use Diagro\Backend\Diagro\MetricService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

/**
 * Measures the execution time of this request and store it in the metric service.
 *
 * @package Diagro\Web\Middleware
 */
class Metric
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
        $user = $request->user();
        $metric = new MetricService($request, $user?->id(), $user?->company()->id(), $request->header('x-parent-metric'));

        $response = $next($request);

        $metric->stop($response);
        $url = config('diagro.service_metric_uri');
        if($url) {
            Http::post($url, $metric->toArray());
        }

        return $response;
    }


}