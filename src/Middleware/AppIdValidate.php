<?php
namespace Diagro\Backend\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

/**
 * Checks if the X-APP-ID header is precense.
 * Validates the application id.
 *
 * @package Diagro\Backend\Middleware
 */
class AppIdValidate
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
        abort_if(! $request->hasHeader('X-APP-ID'), 400, 'Missing header X-APP-ID');

        $url = config('diagro.service_auth_uri') . '/validate/app';
        $response = Http::withHeaders([
            'X-APP-ID' => $request->header('X-APP-ID'),
            'Accept' => 'application/json'
        ])->get($url);
        if(! $response->ok()) {
            $json = $response->json();
            $msg = isset($json['message']) ? $json['message'] : $response->body();
            abort($response->status(), $msg);
        }

        return $next($request);
    }


}
