<?php

namespace BlogCutter\LaravelSessionVisualAnalytics;

use Illuminate\Support\ServiceProvider;

class LaravelSessionVisualAnalyticsServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        // Publish configuration file
        $this->publishes([
            __DIR__ . '/../config/session-analytics.php' => config_path('session-analytics.php'),
        ], 'config');

        // Publish views
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'session-analytics');

        // Publish routes
        $this->loadRoutesFrom(__DIR__ . '/routes/web.php');

        // Publish assets (if any)
        $this->publishes([
            __DIR__ . '/../resources/assets' => public_path('vendor/session-analytics'),
        ], 'assets');

        // Publish views (optional for customization)
        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/session-analytics'),
        ], 'views');

        // Publish migrations (if needed)
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../database/migrations/' => database_path('migrations'),
            ], 'migrations');
        }
    }

    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/session-analytics.php', // The file path
            'session-analytics' // The config key
        );

        $this->loadViewsFrom(__DIR__.'/../views', 'laravel-session-visual-analytics');
    }
}
