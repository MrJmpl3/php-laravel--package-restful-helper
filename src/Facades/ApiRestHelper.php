<?php
/**
 * Copyright (c) 2018.
 * Archivo desarrollado por Jose Manuel Casani Guerra bajo el pseudonimo de MrJmpl3
 *
 * Email: jmpl3.soporte@gmail.com
 * Twitter: @MrJmpl3
 * Pagina Web: https://mrjmpl3-official.es
 */

namespace MrJmpl3\Laravel_Restful_Helper\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class ApiRestHelper
 *
 * @package MrJmpl3\Laravel_Restful_Helper\Facades
 */
class ApiRestHelper extends Facade
{
    /**
     * {@inheritDoc}
     */
    protected static function getFacadeAccessor()
    {
        return '\MrJmpl3\Laravel_Restful_Helper\Helpers\ApiRestHelper';
    }
}
