<?php
/**
 * Copyright (c) 2018.
 * Archivo desarrollado por Jose Manuel Casani Guerra bajo el pseudonimo de MrJmpl3
 *
 * Email: jmpl3.soporte@gmail.com
 * Twitter: @MrJmpl3
 * Pagina Web: https://mrjmpl3-official.es
 */

namespace MrJmpl3\Laravel_Restful_Helper;

use Illuminate\Support\ServiceProvider;

/**
 * Class Laravel_Restful_Helper_ServiceProvider
 *
 * @package MrJmpl3\Laravel_Restful_Helper
 */
class Laravel_Restful_Helper_ServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
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
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/Config/restful-helper.php', 'restful_helper');
    }
}
