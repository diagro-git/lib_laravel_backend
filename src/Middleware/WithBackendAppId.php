<?php
namespace Diagro\Backend\Middleware;

use Closure;
use Diagro\Token\BackendApplicationToken;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * The X-BACKEND-TOKEN is precense and it decodes.
 *
 * @package Diagro\Backend\Middleware
 */
class WithBackendAppId
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
        $has = $request->request->get('has-backend-token',false);
        if($has === false) {
            if($request->hasHeader('x-backend-token')) {
                try {
                    BackendApplicationToken::createFromToken($request->header('x-backend-token'));
                    $has = true;
                } catch(Exception $e) {}
            }
        }

        abort_unless($has, Response::HTTP_UNAUTHORIZED, "No or invalid backend token!");

        return $next($request);
    }


}
