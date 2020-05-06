<?php
/**
 * Copyright (c) 2020.
 * Archivo desarrollado por Jose Manuel Casani Guerra bajo el pseudonimo de MrJmpl3.
 *
 * Email: jmpl3.soporte@gmail.com
 * Twitter: @MrJmpl3
 * Pagina Web: https://mrjmpl3-official.es
 */
namespace MrJmpl3\LaravelRestfulHelper;

use Illuminate\Support\ServiceProvider;

class LaravelRestfulHelperServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/Config/restful-helper.php' => config_path('restful-helper.php'),
        ]);
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/Config/restful-helper.php', 'restful_helper');
    }
}
