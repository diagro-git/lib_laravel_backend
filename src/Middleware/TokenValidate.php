<?php
namespace Diagro\Backend\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

/**
 * Checks if the bearer is precense.
 * Validates the diagro token (AAT token)
 *
 * @package Diagro\Backend\Middleware
 */
class TokenValidate
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
        abort_if(($request->bearerToken() == null), 400, 'Authorization token missing!');

        if($request->get('has-backend-token', false) === false) {
            $url = config('diagro.service_auth_uri') . '/validate/token';
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $request->bearerToken(),
                'X-APP-ID' => $request->header('X-APP-ID'),
                'Accept' => 'application/json'
            ])->get($url);
            if (!$response->ok()) {
                $json = $response->json();
                $msg = isset($json['message']) ? $json['message'] : $response->body();
                abort($response->status(), $msg);
            }
        }

        return $next($request);
    }

}
