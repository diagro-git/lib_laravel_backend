<?php
namespace Diagro\Backend\Middleware;

use Closure;
use Diagro\Token\BackendApplicationToken;
use Exception;
use Illuminate\Http\Request;

/**
 * Checks if the X-BACKEND-TOKEN is precense and if it decodes.
 * This is for speeding up backend to backend requests.
 * The backend already checked APP-ID and TOKEN.
 * No need for furter backend requests in backend.
 *
 * @package Diagro\Backend\Middleware
 */
class BackendAppIdValidate
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
        $request->request->add(['has-backend-token' => false]);

        if($request->hasHeader('x-backend-token')) {
            try {
                BackendApplicationToken::createFromToken($request->header('x-backend-token'));
                $request->request->add(['has-backend-token' => true]);
            } catch(Exception $e) {}
        }

        return $next($request);
    }

    public function terminate($request, $response)
    {
        unset($request['has-backend-token']);
    }


}
