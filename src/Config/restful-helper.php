<?php
/**
 * Copyright (c) 2020.
 * Archivo desarrollado por Jose Manuel Casani Guerra bajo el pseudonimo de MrJmpl3.
 *
 * Email: jmpl3.soporte@gmail.com
 * Twitter: @MrJmpl3
 * Pagina Web: https://mrjmpl3-official.es
 */

return [
    /*
     * Select some fields to response
     *
     * Example: /product?fields=value1,value2,value3,value4
     * In RealWorld: /product?fields=id,name
     */
    'fields' => true,

    /*
     * Filter some fields with data to response
     *
     * Example: /product?value1=data1&value2=data2
     * In RealWorld: /product?id=1&name=test
     */
    'filters' => true,

    /*
     * Sort some fields to response
     *
     * Example : /product?sort=-column1,column2
     * In RealWorld: /product?sort=-id,name
     *
     * With negative prefix = desc
     * Without negative prefix = asc
     */
    'sorts' => true,

    /*
     * Paginate to response
     *
     * Example: /product?paginate=true
     * In RealWorld: /product?paginate=true
     *
     * You can select the amount data per page
     *
     * xample 2: /product?paginate=true&per_page=5
     * In RealWorld 2: /product?paginate=true&per_page=5
     */
    'paginate' => true,
];
