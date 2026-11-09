<?php

namespace Imranbru99\SessionVisualAnalytics;

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Imranbru99\SessionVisualAnalytics\Http\Middleware\TrackSessionVista;
use Imranbru99\SessionVisualAnalytics\Services\SessionAnalyticsService;

class SessionVisualAnalyticsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/session-visual-analytics.php', 'session-visual-analytics');

        $this->app->singleton(SessionAnalyticsService::class);
        $this->app->alias(SessionAnalyticsService::class, 'session-vista');
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/session-visual-analytics.php' => config_path('session-visual-analytics.php'),
        ], 'session-vista-config');

        $this->publishes([
            __DIR__.'/../database/migrations/' => database_path('migrations'),
        ], 'session-vista-migrations');

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/session-visual-analytics'),
        ], 'session-vista-views');

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'session-visual-analytics');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        /** @var Router $router */
        $router = $this->app->make(Router::class);
        $router->aliasMiddleware('session-vista.track', TrackSessionVista::class);

        if (config('session-visual-analytics.track.enabled')) {
            $router->pushMiddlewareToGroup('web', TrackSessionVista::class);
        }

        $this->registerRoutes();
    }

    protected function registerRoutes(): void
    {
        Route::group([
            'prefix' => config('session-visual-analytics.path', 'session-vista'),
            'middleware' => config('session-visual-analytics.middleware', ['web', 'auth']),
            'as' => 'session-vista.',
        ], function () {
            $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        });
    }
}
