<?php

return [
    /*
     * Select some fields to response
     *
     * Example: /users?fields[users]=name,email
     * Example legacy: /users?fields=name,email
     */

    'fields' => true,

    /*
     * Filter some fields with data to response
     *
     * Example: /users?filter[name]=john&filter[email]=gmail
     * Example legacy: /product?name=john&email=gmail
     */

    'filters' => true,

    /*
     * Sort some fields to response
     *
     * Example: /users?sort=-name,email
     * Example legacy: /users?sort=-name,email
     *
     * With negative prefix = desc
     * Without negative prefix = asc
     */
    'sorts' => true,

    /*
     * Paginate to response
     *
     * Example: /users?paginate=true&page[size]=5&page[number]=1
     * Example legacy: /users?paginate=true&per_page=5&page=1
     */
    'paginate' => true,

    /*
     * Structures of the parsing data
     *
     * Model = Model to apply (Not repeat)
     *  route = Route to apply
     *  transformerName = Name of the field which contains the keys to transforms
     */
    'structures' => [
        //        [
        //            'model' => \MrJmpl3\LaravelRestfulHelper\Tests\Classes\TestModel::class,
        //            'data' => [
        //                [
        //                    'routes' => ['test.index', 'test.show'],
        //                    'transformer' => 'transformersV1',
        //                    'fieldGroupName' => 'fieldGroupNameV1',
        //                    'allowedFields' => 'allowedFieldsV1',
        //                    'allowedFilters' => 'allowedFiltersV1',
        //                    'allowedSorts' => 'allowedSortsV1',
        //                    'allowedRelations' => 'allowedRelationsV1',
        //                ],
        //            ],
        //        ],
    ],
];
