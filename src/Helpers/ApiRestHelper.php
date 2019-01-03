<?php
/**
 * Copyright (c) 2018.
 * Archivo desarrollado por Jose Manuel Casani Guerra bajo el pseudonimo de MrJmpl3
 *
 * Email: jmpl3.soporte@gmail.com
 * Twitter: @MrJmpl3
 * Pagina Web: https://mrjmpl3-official.es
 */

namespace MrJmpl3\Laravel_Restful_Helper\Helpers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ApiRestHelper
{
    /**
     * @return string
     */
    public function test()
    {
        return 'Test';
    }

    /**
     * Retorna los "fields" de la peticion en un arreglo
     *
     * @return array
     */
    public function getQueryFields()
    {
        $fields = [];

        if (request()->has('fields')) {
            $fields = explode(',', request()->get('fields'));
        }

        // [
        //  "column1",
        //  "column2",
        //  "column3",
        //  "column4"
        // ]

        return $fields;
    }

    /**
     * Retorna los "fields" validados de la peticion comparando el "apiTransforms" del modelo
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     *
     * @return array
     */
    public function getQueryFieldsValidated($model)
    {
        // Obtenemos los "fields" de la peticion

        $fields = $this->getQueryFields();

        // Obtengo la tabla del "Builder"

        $table = $model->getTable();

        // Obtengo las columnas de la tabla

        $columns = Schema::getColumnListing($table);

        // Buscamos si la propiedad "apiTransforms" existe en el "Model"

        $apiTransforms = [];

        if (property_exists($model, 'apiTransforms')) {
            $apiTransforms = $model->apiTransforms;
        }

        // Arreglo donde almacenara los "fields" validados

        $validatedFields = [];

        // Recorro en cada columna de la peticion

        foreach ($fields as $field) {

            // Verifico si existe en el "apiTransforms"
            // Obtengo la columna real segun el "apiTransforms"
            // Si no encuentra es el "apiTransforms" se entiende que es una columna real

            $validatedColumn = array_get($apiTransforms, $field, $field);

            // Validamos que la columna real existe en la tabla

            if (array_has($columns, $validatedColumn)) {

                // Ya que si existe lo agregamos a la lista de seleccion

                $validatedFields[] = $validatedColumn;
            }
        }

        // Retorno los "fields" convertidos

        return $validatedFields;
    }

    /**
     * Retorna los filtros de la peticion en un arreglo
     *
     * @return array
     */
    public function getQueryFilters()
    {
        // Obtengo todos los parametros de la peticion

        $requests = request()->all();

        // Arreglo donde se guardara los filtros

        $filters = [];

        // Recorro los parametros

        foreach ($requests as $key => $value) {

            // Verifico que la peticion no sea una consulta reservada

            if ($key !== 'sort' && $key !== 'fields' && $key !== 'embed' && $key !== 'paginate') {
                $filters = array_add($filters, $key, $value);
            }
        }

        // Retorno los filtros
        //
        // [
        //  "column": "value",
        //  "column2": "value2"
        // ]

        return $filters;
    }

    /**
     * Retorna los "fields" convertidos de la peticion comparando el "apiTransforms" del modelo
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     *
     * @return array
     */
    public function getQueryFiltersValidated($model)
    {
        // Obtenemos los "filter" de la peticion

        $filters = $this->getQueryFilters();

        // Obtengo la tabla del "Builder"

        $table = $model->getTable();

        // Obtengo las columnas de la tabla

        $columns = Schema::getColumnListing($table);

        // Buscamos si la propiedad "apiTransforms" existe en el "Model"

        $apiTransforms = [];

        if (property_exists($model, 'apiTransforms')) {
            $apiTransforms = $model->apiTransforms;
        }

        // Buscamos si la propiedad "apiExcludeFilter" existe en el "Model"

        $apiExcludeFilter = [];

        if (property_exists($model, 'apiExcludeFilter')) {
            $apiExcludeFilter = $model->apiExcludeFilter;
        }

        // Arreglo donde almacenara los "fields" convertidos

        $validatedFilters = [];

        // Recorro en cada columna de la peticion

        foreach ($filters as $key => $value) {

            // Verifico si existe en el "apiTransforms"
            // Obtengo la columna real segun el "apiTransforms"
            // Si no encuentra es el "apiTransforms" se entiende que es una columna real

            $validatedColumn = array_get($apiTransforms, $key, $key);

            // Validamos que la columna real existe en la tabla y no este excluido

            if (array_has($columns, $validatedColumn) && !array_has($apiExcludeFilter, $validatedColumn)) {

                // Ya que si existe lo agregamos a la lista de "filters"

                $validatedFilters = array_add($validatedFilters, $validatedColumn, $value);
            }
        }

        // Retorno los filtros convertidos
        return $validatedFilters;
    }

    public function getQuerySorts()
    {
        $sorts = [];

        if (request()->has('sort')) {
            $querySorts = explode(',', request()->get('sort'));

            foreach ($querySorts as $querySort) {
                $lenQuerySort = strlen($querySort);

                if ($lenQuerySort > 0) {
                    if (substr($querySort, 0, 1) === '-') {
                        $sorts = array_add($sorts, substr($querySort, 1, $lenQuerySort - 1), 'desc');
                    } else {
                        $sorts = array_add($sorts, $querySort, 'asc');
                    }
                }
            }
        }

        // Retorno el sorts de la peticion
        //
        // [
        //  "column1": "desc",
        //  "column2": "asc"
        // ]

        return $sorts;
    }

    /**
     * Retorna los "sorts" convertidos de la peticion comparando el "apiTransforms" del modelo
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     *
     * @return array
     */
    public function getQuerySortsValidated($model)
    {

        // Obtengo los "sorts" de la peticion

        $sorts = $this->getQuerySorts();

        // Obtengo la tabla del "Builder"

        $table = $model->getTable();

        // Obtengo las columnas de la tabla

        $columns = Schema::getColumnListing($table);

        // Buscamos si la propiedad "apiTransforms" existe en el "Model"

        $apiTransforms = [];

        if (property_exists($model, 'apiTransforms')) {
            $apiTransforms = $model->apiTransforms;
        }

        // Arreglo donde almacenara los "fields" convertidos

        $convertedSorts = [];

        // Recorro en cada columna de la peticion

        foreach ($sorts as $key => $value) {

            // Verifico si existe en el "apiTransforms"
            // Obtengo la columna real segun el "apiTransforms"
            // Si no encuentra es el "apiTransforms" se entiende que es una columna real

            $convertedColumn = array_get($apiTransforms, $key, $key);

            // Validamos que la columna real existe en la tabla

            if (array_has($columns, $convertedColumn)) {

                // Ya que si existe lo agregamos a la lista de "sorts"

                $convertedSorts = array_add($convertedSorts, $convertedColumn, $value);
            }
        }

        // Retorno los filtros convertidos

        return $convertedSorts;
    }

    /**
     * @return array
     */
    public function getQueryPaginate()
    {
        $perPage = 15;

        if (request()->has('per_page')) {
            $rulesPerPage = [
                'per_page' => 'integer|min:2|max:200',
            ];

            try {
                Validator::validate(request()->all(), $rulesPerPage);

                $perPage = intval(request()->get('per_page'));
            } catch (ValidationException $e) {
            }
        }

        $paginate = TRUE;

        if (request()->has('paginate')) {
            $paginate = (request()->input('paginate') === 'true');
        }

        return [
            'paginate' => $paginate,
            'per_page' => $perPage,
        ];
    }

    public function getQueryEmbed() {

        // Lista de relaciones en la peticion "embed"

        $embedRelations = [];

        // Se separa la relaciones en la peticion mediante una "," , si es una accion forzada entonces asigna todas las relaciones

        if (request()->has('embed')) {
            $embedRelations = explode(',', request()->get('embed'));
        }

        // Lista de 'fields' de la relaciones

        $embed = [];

        // Se separa las relaciones mediantes un "." y se agrupan segun su pertenecia en la relacion

        foreach ($embedRelations as $embedRelation) {
            $parts = explode('.', $embedRelation);

            if (count($parts) > 1) {
                $embed[$parts[0]][] = $parts[1];
            } else {
                $embed[$parts[0]] = [];
            }
        }

        return $embed;
    }

    /**
     * Sirve para obtener la lista de relaciones en la peticion
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param bool                                     $forced
     *
     * @return array
     */
    public function getQueryEmbedValidated($model, bool $forced = FALSE)
    {
        $embed = $this->getQueryEmbed();

        $apiAcceptRelations = [];

        // Verifico que existe la propiedad "apiAcceptRelations"

        if (property_exists($model, 'apiAcceptRelations')) {
            $apiAcceptRelations = $model->apiAcceptRelations;
        }

        if($forced) {
            $embed = $apiAcceptRelations;
        }

        // Lista de relaciones aceptadas por el sistema

        $embedValidated = [];

        foreach ($embed as $key => $val) {

            // Verifico que el metodo existe en el "Model" y verifico que la relacion obtenida esta permitido en el "Model"

            if (method_exists($model, $key) && (array_has($apiAcceptRelations, $key))) {
                $embedValidated[$key] = $val;
            }
        }

        // Retorno la relaciones validadas

        return $embedValidated;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $model Model to get the apiTransforms
     * @param string                              $key   Key to get the fields from embed
     *
     * @return array
     */
    public function getQueryEmbedFieldsValidate(Model $model, string $key) {
        $embed = $this->getQueryEmbed();

        $fieldsFromEmbed = array_get($embed, $key, []);

        // Obtengo la tabla del "Builder"

        $table = $model->getTable();

        // Obtengo las columnas de la tabla

        $columns = Schema::getColumnListing($table);

        // Buscamos si la propiedad "apiTransforms" existe en el "Model"

        $apiTransforms = [];

        if (property_exists($model, 'apiTransforms')) {
            $apiTransforms = $model->apiTransforms;
        }

        // Arreglo donde almacenara los "fields" validados

        $validatedFields = [];

        foreach ($fieldsFromEmbed as $fieldFromEmbed) {
            // Verifico si existe en el "apiTransforms"
            // Obtengo la columna real segun el "apiTransforms"
            // Si no encuentra es el "apiTransforms" se entiende que es una columna real

            $validatedColumn = array_get($apiTransforms, $fieldFromEmbed, $fieldFromEmbed);

            // Validamos que la columna real existe en la tabla

            if (array_has($columns, $validatedColumn)) {

                // Ya que si existe lo agregamos a la lista de seleccion

                $validatedFields[] = $validatedColumn;
            }
        }

        return $validatedFields;
    }

    /**
     * Sirve para convertir un "Model" a una respuesta para un "Resource Collection"
     * El segundo argumento es un arreglo de tablas en la cual no queremos que sean filtradas en la consulta
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     *
     * @param array                               $blockFilter
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model
     */
    public function responseToResourceCollection($model, $blockFilter = [])
    {
        // Se copia el Model para mantener una inmutabilidad
        $query = $model;

        // Proceso para la seleccion de los datos
        if (config('restful_helper.fields')) {
            $query = $this->apiFields($query, $model);
        }

        // Proceso para el filtro o clausulas de los datos
        if (config('restful_helper.filters')) {
            $query = $this->apiFilter($query, $model, $blockFilter);
        }

        // Proceso para el ordenamiento de los datos
        if (config('restful_helper.sorts')) {
            $query = $this->apiSort($query, $model);
        }

        // Proceso para la paginacion
        if (config('restful_helper.paginate')) {
            $query = $this->apiPaginate($query);
        }

        // Retorno la consulta con los datos procesado
        return $query;
    }

    /**
     * Sirve para convertir un "Builder" a una respuesta para un "Resource Collection"
     * El segundo argumento es un arreglo de tablas en la cual no queremos que sean filtradas en la consulta
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param array                                 $blockFilter
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model
     */
    public function responseFromBuilderToResourceCollection($builder, $blockFilter = [])
    {
        // Se obtiene el modelo del "Builder"
        $model = $builder->getModel();

        // Se copia el "Builder" para mantener una inmutabilidad
        $query = $builder;

        // Proceso para la seleccion de los datos
        if (config('restful_helper.fields')) {
            $query = $this->apiFields($query, $model);
        }

        // Proceso para el filtro o clausulas de los datos
        if (config('restful_helper.filters')) {
            $query = $this->apiFilter($query, $model, $blockFilter);
        }

        // Proceso para el ordenamiento de los datos
        if (config('restful_helper.sorts')) {
            $query = $this->apiSort($query, $model);
        }

        // Proceso para la paginacion
        if (config('restful_helper.paginate')) {
            $query = $this->apiPaginate($query);
        }

        // Retorno la consulta con los datos procesado
        return $query;
    }

    /**
     * Sirve para convertir un "Builder" a una respuesta para un "Resource"
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function responseFromBuilderToResource($builder)
    {
        $model = $builder->getModel();

        $fields = $this->getQueryFieldsValidated($model);

        if (count($fields) > 0) {
            $builder = $builder->select($fields);
        }

        // Retornamos un "Model" que sera usado por el "Resource"
        return $builder->first();
    }

    /**
     * Proceso para la seleccion de los datos
     *
     * @param \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Builder $query
     * @param \Illuminate\Database\Eloquent\Model                                       $model
     *
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Builder
     */
    private function apiFields($query, $model)
    {
        $convertedFields = $this->getQueryFieldsValidated($model);

        // Verificamos si la selecciones reales tiene contenido para evitar hacer una consulta innecesaria
        if (count($convertedFields) > 0) {
            $query = $query->select($convertedFields);
        }

        // Retornamos un "Builder"
        return $query;
    }

    /**
     * Proceso para el filtro o clausulas de los datos
     *
     * @param \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Builder $query
     * @param \Illuminate\Database\Eloquent\Model                                       $model
     *
     * @param array                                                                     $blockFilter
     *
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Builder
     */
    private function apiFilter($query, $model, $blockFilter = [])
    {
        $convertedFilters = $this->getQueryFiltersValidated($model);

        // Obtengo los casteos del "Model"
        $casts = $model->getCasts();

        foreach ($convertedFilters as $key => $value) {
            if (!array_has($blockFilter, $key)) {
                if ($value === '') {
                    $query = $query->where($key, '=', NULL);
                } else {
                    switch ($casts[$key]) {
                        case 'int':
                        case 'integer':
                            $query = $query->where($key, '=', intval($value));
                            break;
                        case 'real':
                        case 'float':
                        case 'double':
                            $query = $query->where($key, '=', floatval($value));
                            break;
                        case 'string':
                            $query = $query->where($key, '=', strval($value));
                            break;
                        case 'bool':
                        case 'boolean':
                            $query = $query->where($key, '=', boolval($value));
                            break;
                        case 'date':
                            $query = $query->where($key, '=', $value);
                            break;
                    }
                }
            }
        }

        return $query;
    }

    /**
     * Proceso para el ordenamiento de los datos
     *
     * @param \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Builder $query
     * @param \Illuminate\Database\Eloquent\Model                                       $model
     *
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Builder
     */
    private function apiSort($query, $model)
    {
        $convertedSorts = $this->getQuerySortsValidated($model);

        foreach ($convertedSorts as $key => $value) {
            $query = $query->orderBy($key, $value);
        }

        return $query;
    }

    /**
     * Proceso para la paginacion
     *
     * @param \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Model
     */
    private function apiPaginate($query)
    {
        $paginateArr = $this->getQueryPaginate();

        if (array_get($paginateArr, 'paginate', TRUE)) {
            return $query->paginate(array_get($paginateArr, 'per_page', 15))
                         ->appends(request()->all());
        } else {
            return $query->get();
        }
    }
}
