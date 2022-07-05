<?php
namespace Diagro\Backend;

use Diagro\Backend\Console\Commands\BackendTokenGenerator;
use Diagro\Backend\Console\Commands\DiagroPolicy;
use Diagro\Backend\Console\Commands\DiagroRights;
use Diagro\Backend\Diagro\MetricService;
use Diagro\Backend\Middleware\AppIdValidate;
use Diagro\Backend\Middleware\AuthorizedApplication;
use Diagro\Backend\Middleware\BackendAppIdValidate;
use Diagro\Backend\Middleware\CacheResource;
use Diagro\Backend\Middleware\Localization;
use Diagro\Backend\Middleware\TokenValidate;
use Diagro\Token\ApplicationAuthenticationToken;
use Diagro\Token\Auth\TokenProvider;
use Exception;
use Illuminate\Foundation\Http\Events\RequestHandled;
use Illuminate\Foundation\Http\Kernel;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

/**
 * Bridge between package and laravel backend application.
 *
 * @package Diagro\Backend
 */
class DiagroServiceProvider extends ServiceProvider
{


    public function register()
    {
        $this->app->singleton(ApplicationAuthenticationToken::class, function() {
            $token = request()->bearerToken();
            return ApplicationAuthenticationToken::createFromToken($token);
        });

        $this->app->singleton(MetricService::class);
    }


    /**
     * Boot me up Scotty!
     */
    public function boot(Kernel $kernel)
    {
        //add Diagro AAT driver
        auth()->viaRequest('diagro-aat', function(Request $request) {
            $token = $request->bearerToken();
            if($token != null) {
                try {
                    //return User Token model
                    $aat = ApplicationAuthenticationToken::createFromToken($token);
                    if($aat instanceof ApplicationAuthenticationToken) {
                        return $aat->user();
                    }
                } catch(Exception $e) {
                    return null;
                }
            }

            return null;
        });

        //register the auth providers
        Auth::provider('token', function($app, $config) {
            return new TokenProvider($config['token_class_name']);
        });

        //configuration
        $this->publishes([
            __DIR__ . '/../configs/diagro.php' => config_path('diagro.php'),
            __DIR__ . '/../configs/auth.php' => config_path('auth.php'),
            __DIR__ . '/../configs/logging.php' => config_path('logging.php'),
            __DIR__ . '/../routes/api.php' => base_path('routes/api.php'),
            __DIR__ . '/../routes/backend.php' => base_path('routes/backend.php'),
        ]);

        //middleware
        /** @var Router $router */
        $router = $this->app->make(Router::class);
        $router->pushMiddlewareToGroup('api', BackendAppIdValidate::class);
        $router->pushMiddlewareToGroup('api', AppIdValidate::class);
        $router->pushMiddlewareToGroup('api', TokenValidate::class);
        $router->pushMiddlewareToGroup('api', AuthorizedApplication::class);
        $router->pushMiddlewareToGroup('api', CacheResource::class);
        $router->pushMiddlewareToGroup('api', Localization::class);
        $kernel->prependToMiddlewarePriority(TokenValidate::class);
        $kernel->prependToMiddlewarePriority(AppIdValidate::class);
        $kernel->prependToMiddlewarePriority(BackendAppIdValidate::class);

        $this->app->booted(function() {
            app(MetricService::class);
        });
        Event::listen(RequestHandled::class, function(RequestHandled $event) {
            app(MetricService::class)->stop($event->request, $event->response);
            app(MetricService::class)->send();
        });

        //drop invalid keys
        Validator::excludeUnvalidatedArrayKeys();

        //so you can use ->can('read', 'model) on the route
        Route::macro('can', function($abbility, ... $params) {
            $params = implode('|', $params);
            $this->middleware('can:' . $abbility . ',' . $params);
            return $this;
        });

        //always use https in production, ALWAYS! It's the future
        URL::forceScheme('https');

        //commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                BackendTokenGenerator::class,
                DiagroRights::class,
                DiagroPolicy::class,
            ]);
        }
    }


}