<?php
/**
 * Copyright (c) 2018.
 * Archivo desarrollado por Jose Manuel Casani Guerra bajo el pseudonimo de MrJmpl3
 *
 * Email: jmpl3.soporte@gmail.com
 * Twitter: @MrJmpl3
 * Pagina Web: https://mrjmpl3-official.es
 */

return [
    /**
     * Sirve para seleccionar datos / Select some fields to response
     *
     * Ejemplo / Example: /product?fields=value1,value2,value3,value4
     * En el mundo real / In RealWorld: /product?fields=id,name
     */
    'fields' => true,

    /**
     * Sirve para filtrar datos / Filter some fields with data to response
     *
     * Ejemplo / Example: /product?value1=data1&value2=data2
     * En el mundo real / In RealWorld: /product?id=1&name=test
     */
    'filters' => true,

    /**
     * Sirve para ordenar datos / Sort some fields to response
     *
     * Ejemplo / Example : /product?sort=-column1,column2
     * En el mundo real / In RealWorld: /product?sort=-id,name
     *
     * Con prefijo negativo / With negative prefix = desc
     * Sin prefijo negativo / Without negative prefix = asc
     */
    'sorts' => true,

    /**
     * Sirve para paginar las respuestas / Paginate to response
     *
     * Ejemplo / Example: /product?paginate=true
     * En el mundo real / In RealWorld: /product?paginate=true
     *
     * Puedes seleccionar la cantidad de datos por paginas / You can select the amount data per page
     *
     * Ejemplo 2 / Example 2: /product?paginate=true&per_page=5
     * En el mundo real 2/ In RealWorld 2: /product?paginate=true&per_page=5
     */
    'paginate' => true
];
