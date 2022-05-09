<?php
namespace Diagro\Backend\Middleware;

use Closure;
use Diagro\Token\Model\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

/**
 * Checks if this backend application is in the AAT token.
 *
 * @package Diagro\Backend\Middleware
 */
class AuthorizedApplication
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
        if($request->get('has-backend-token', false) === false) {
            /** @var User $user */
            $user = $request->user();
            abort_unless(($user && $user->hasApplication(config('diagro.app_name'))), 403, 'Access denied to the application!');
        }

        return $next($request);
    }

}
