<?php
namespace MrJmpl3\LaravelRestfulHelper;

use Illuminate\Support\ServiceProvider;

class LaravelRestfulHelperServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole() && \function_exists('config_path')) {
            $this->publishes([
                __DIR__.'/../config/restful_helper.php' => config_path('restful_helper.php'),
            ]);
        }

        $this->mergeConfigFrom(__DIR__.'/../config/restful_helper.php', 'restful_helper');
    }
}
