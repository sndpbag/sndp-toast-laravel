<?php

namespace sndpbag\LaravelToast;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class ToastServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('toast', function ($app) {
            return new ToastManager($app['session.store']);
        });
    }

    public function boot()
    {
        // Load views
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'toast');

        // Publish config
        $this->publishes([
            __DIR__.'/../config/toast.php' => config_path('toast.php'),
        ], 'toast-config');

        // Publish views
        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/toast'),
        ], 'toast-views');

        // Publish assets
        $this->publishes([
            __DIR__.'/../resources/js' => public_path('vendor/toast'),
        ], 'toast-assets');

        // Merge config
        $this->mergeConfigFrom(__DIR__.'/../config/toast.php', 'toast');

        // Register Blade directive
        Blade::directive('sndpToast', function () {
            return "<?php echo view('toast::container')->render(); ?>";
        });
    }
}