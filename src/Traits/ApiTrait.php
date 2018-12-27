<?php
/**
 * Copyright (c) 2018.
 * Archivo desarrollado por Jose Manuel Casani Guerra bajo el pseudonimo de MrJmpl3
 *
 * Email: jmpl3.soporte@gmail.com
 * Twitter: @MrJmpl3
 * Pagina Web: https://mrjmpl3-official.es
 */

namespace MrJmpl3\Laravel_Restful_Helper\Traits;

use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

/**
 * Trait ApiTrait
 *
 * @package MrJmpl3\Laravel_Restful_Helper\Traits
 */
trait ApiTrait
{
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
    protected function executeApiResponseToRC($model, $blockFilter = [])
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
    protected function executeApiResponseFromBuilderToRC($builder, $blockFilter = [])
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
    protected function executeApiResponseFromBuilderToResource($builder)
    {
        if (request()->has('fields')) {

            // Obtengo la tabla del "Builder"

            $table = $builder->getModel()
                             ->getTable();

            // Obtengo las columnas de la tabla

            $columns = Schema::getColumnListing($table);

            // Separo las selecciones mediante una coma

            $queryFields = explode(',', request()->get('fields'));

            // Buscamos si la propiedad "apiTransforms" existe en el "Model"

            $apiTransforms = [];

            if (property_exists($builder->getModel(), 'apiTransforms')) {
                $apiTransforms = $builder->getModel()->apiTransforms;
            }

            // Arreglo donde almacenara las selecciones reales

            $selectRealColumns = [];

            // Recorro en cada columna de la peticion

            foreach ($queryFields as $queryField) {

                // Obtengo la columna real segun el "apiTransforms"

                $realColumn = array_search($queryField, $apiTransforms);

                // Si no encuentra es el "apiTransforms" se entiende que es una columna real

                if ($realColumn === FALSE) {
                    $realColumn = $queryField;
                }

                // Validamos que la columna real existe en la tabla

                if (in_array($realColumn, $columns, TRUE)) {

                    // Ya que si existe lo agregamos a la lista de seleccion

                    $selectRealColumns[] = $realColumn;
                }
            }

            // Verificamos si la selecciones reales tiene contenido para evitar hacer una consulta innecesaria

            if (count($selectRealColumns) > 0) {
                $builder = $builder->select($selectRealColumns);
            }
        }

        // Retornamos un "Model" que sera usado por el "Resource"

        return $builder->first();
    }

    /**
     * Sirve para obtener la lista de relaciones en la peticion
     *
     * @param \Illuminate\Http\Resources\Json\Resource $resource
     * @param bool                                     $forced
     *
     * @return array
     */
    protected function embed(Resource $resource, bool $forced = FALSE)
    {
        $apiAcceptRelations = [];

        // Verifico que existe la propiedad "apiAcceptRelations"

        if (property_exists($resource->resource, 'apiAcceptRelations')) {
            $apiAcceptRelations = $resource->resource->apiAcceptRelations;
        }

        // Lista de relaciones en la peticion "embed"

        $embedListOfRelations = [];

        // Se separa la relaciones en la peticion mediante una "," , si es una accion forzada entonces asigna toda la relaciones

        if ($forced) {
            $embedListOfRelations = $apiAcceptRelations;
        } elseif (request()->has('embed')) {
            $embedListOfRelations = explode(',', request()->get('embed'));
        }

        // Lista de selecciones de la relaciones

        $embedListOfSelectsFromRelations = [];

        // Se separa las relaciones mediantes un "." y se agrupan segun su pertenecia en la relacion

        foreach ($embedListOfRelations as $embedListOfRelation) {
            $parts = explode('.', $embedListOfRelation);

            if (count($parts) > 1) {
                $embedListOfSelectsFromRelations[$parts[0]][] = $parts[1];
            } else {
                $embedListOfSelectsFromRelations[$parts[0]] = [];
            }
        }

        // Lista de relaciones aceptadas por el sistema

        $responsesEmbed = [];

        foreach ($embedListOfSelectsFromRelations as $key => $val) {

            // Verifico que el metodo existe en el "Model" y verifico que la relacion obtenida esta permitido en el "Model"

            if (method_exists($resource->resource, $key) && (in_array($key, $apiAcceptRelations))) {
                $responsesEmbed[$key] = $val;
            }
        }

        // Retorno la relaciones validadas

        return $responsesEmbed;
    }

    /**
     * Sirve para convertir un arreglo en un "Model" o relacion, para ser usado por un "Resource"
     *
     * Esta funcion sera precariada proximanente
     *
     * @param $modelOrRelation
     * @param $arrayFields
     *
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    protected function apiFieldsFromArrayToResource($modelOrRelation, $arrayFields)
    {
        // "Model" con el cual vamos a obtener los datos

        $model = NULL;

        if (get_class($modelOrRelation) === 'Illuminate\Database\Eloquent\Relations\BelongsTo' || get_class($modelOrRelation) === 'Illuminate\Database\Eloquent\Relations\HasOne') {

            // Se obtiene el modelo de la relacion

            /** @var \Illuminate\Database\Eloquent\Relations\BelongsTo $modelOrRelation */
            $model = $modelOrRelation->getModel();
        } else {
            $model = $modelOrRelation;
        }

        // Se obtiene las columnas de la tabla del "Model"

        /** @var \Illuminate\Database\Eloquent\Model $model */
        $columns = Schema::getColumnListing($model->getTable());

        // Buscamos si la propiedad "apiTransforms" existe en el "Model"

        $apiTransforms = [];

        if (property_exists($model, 'apiTransforms')) {
            $apiTransforms = $model->apiTransforms;
        }

        // Lista de selecciones reales

        $selectRealColumns = [];

        // Recorro en cada columna de la peticion

        foreach ($arrayFields as $arrayField) {

            // Obtengo la columna real segun el "apiTransforms"

            $realColumn = array_search($arrayField, $apiTransforms);

            // Si no encuentra es el "apiTransforms" se entiende que es una columna real

            if ($realColumn === FALSE) {
                $realColumn = $arrayField;
            }

            // Validamos que la columna real existe en la tabla

            if (in_array($realColumn, $columns, TRUE)) {

                // Ya que si existe lo agregamos a la lista de seleccion

                $selectRealColumns[] = $realColumn;
            }
        }

        // Verificamos si la selecciones reales tiene contenido para evitar hacer una consulta innecesaria

        if (count($selectRealColumns) > 0) {
            return $modelOrRelation->select($selectRealColumns);
        } else {
            return $modelOrRelation;
        }
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
        if (request()->has('fields')) {

            // Obtengo la tabla del "Builder"

            $table = $model->getTable();

            // Obtengo las columnas de la tabla

            $columns = Schema::getColumnListing($table);

            // Separo las selecciones mediante una coma

            $queryFields = explode(',', request()->get('fields'));

            // Buscamos si la propiedad "apiTransforms" existe en el "Model"

            $apiTransforms = [];

            if (property_exists($model, 'apiTransforms')) {
                $apiTransforms = $model->apiTransforms;
            }

            // Arreglo donde almacenara las selecciones reales

            $selectRealColumns = [];

            // Recorro en cada columna de la peticion

            foreach ($queryFields as $queryField) {

                // Obtengo la columna real segun el "apiTransforms"

                $realColumn = array_search($queryField, $apiTransforms);

                // Si no encuentra es el "apiTransforms" se entiende que es una columna real

                if ($realColumn === FALSE) {
                    $realColumn = $queryField;
                }

                // Validamos que la columna real existe en la tabla

                if (in_array($realColumn, $columns, TRUE)) {

                    // Ya que si existe lo agregamos a la lista de seleccion

                    $selectRealColumns[] = $realColumn;
                }
            }

            // Verificamos si la selecciones reales tiene contenido para evitar hacer una consulta innecesaria

            if (count($selectRealColumns) > 0) {
                $query = $query->select($selectRealColumns);
            }
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
        // Obtengo la tabla del "Model"

        $table = $model->getTable();

        // Obtengo las columnas de la tabla

        $columns = Schema::getColumnListing($table);

        // Obtengo los casteos del "Model"

        $casts = $model->getCasts();

        // Buscamos si la propiedad "apiExcludeFilter" existe en el "Model"

        $apiExcludeFilter = [];

        if (property_exists($model, 'apiExcludeFilter')) {
            $apiExcludeFilter = $model->apiExcludeFilter;
        }

        // Buscamos si la propiedad "apiExcludeFilter" existe en el "Model"

        $apiExcludeFilter = [];

        if (property_exists($model, 'apiExcludeFilter')) {
            $apiExcludeFilter = $model->apiExcludeFilter;
        }

        // Buscamos si la propiedad "apiTransforms" existe en el "Model"

        $apiTransforms = [];

        if (property_exists($model, 'apiTransforms')) {
            $apiTransforms = $model->apiTransforms;
        }

        // Recorro en cada columna de la tabla del "Model"

        foreach ($columns as $column) {
            if (!in_array($column, $apiExcludeFilter) && !in_array($column, $blockFilter)) {
                if (array_key_exists($column, $apiTransforms)) {

                    // Obtengo la columna falsa

                    $fakeColumn = $apiTransforms[$column];

                    if (request()->has($fakeColumn)) {
                        if (request()->input($fakeColumn) === '') {
                            $query = $query->where($column, '=', NULL);
                        } else {
                            switch ($casts[$column]) {
                                case 'int':
                                case 'integer':
                                    $query = $query->where($column, '=', intval(request()->input($fakeColumn)));
                                    break;
                                case 'real':
                                case 'float':
                                case 'double':
                                    $query = $query->where($column, '=', floatval(request()->input($fakeColumn)));
                                    break;
                                case 'string':
                                    $query = $query->where($column, '=', strval(request()->input($fakeColumn)));
                                    break;
                                case 'bool':
                                case 'boolean':
                                    $query = $query->where($column, '=', boolval(request()->input($fakeColumn)));
                                    break;
                                case 'date':
                                case 'datetime':
                                    $fecha = Carbon::createFromFormat('d/m/Y', request()->input($fakeColumn), 'America/Lima')
                                                   ->format('Y-m-d');

                                    $query = $query->where($column, '=', $fecha);
                                    break;
                            }
                        }
                    }
                } else {
                    if (request()->has($column)) {
                        if (request()->input($column) === '') {
                            $query = $query->where($column, '=', NULL);
                        } else {
                            switch ($casts[$column]) {
                                case 'int':
                                case 'integer':
                                    $query = $query->where($column, '=', intval(request()->input($column)));
                                    break;
                                case 'real':
                                case 'float':
                                case 'double':
                                    $query = $query->where($column, '=', floatval(request()->input($column)));
                                    break;
                                case 'string':
                                    $query = $query->where($column, '=', strval(request()->input($column)));
                                    break;
                                case 'bool':
                                case 'boolean':
                                    $query = $query->where($column, '=', boolval(request()->input($column)));
                                    break;
                                case 'date':
                                case 'datetime':
                                    $fecha = Carbon::createFromFormat('d/m/Y', request()->input($column), 'America/Lima')
                                                   ->format('Y-m-d');

                                    $query = $query->where($column, '=', $fecha);
                                    break;
                            }
                        }
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
        if (request()->has('sort')) {

            // Obtengo la consulta de ordenamiento y la separo por una ","

            $querySortBy = explode(',', request()->get('sort'));

            // Se obtiene las columnas de la tabla del "Model"

            $columns = Schema::getColumnListing($model->getTable());

            // Buscamos si la propiedad "apiTransforms" existe en el "Model"

            $apiTransforms = [];

            if (property_exists($model, 'apiTransforms')) {
                $apiTransforms = $model->apiTransforms;
            }

            // Recorro cada columna de la tabla

            foreach ($columns as $column) {
                if (array_key_exists($column, $apiTransforms)) {
                    if (($i = array_search($apiTransforms[$column], $querySortBy)) !== FALSE) {
                        $query = $query->orderBy($column, 'asc');
                    } elseif (($i = array_search('-' . $apiTransforms[$column], $querySortBy)) !== FALSE) {
                        $query = $query->orderBy($column, 'desc');
                    }
                } else {
                    if (($i = array_search($column, $querySortBy)) !== FALSE) {
                        $query = $query->orderBy($column, 'asc');
                    } elseif (($i = array_search('-' . $column, $querySortBy)) !== FALSE) {
                        $query = $query->orderBy($column, 'desc');
                    }
                }
            }

            // Retorno un "Builder"

            return $query;
        } else {

            // Por defecto se ordenara por id de forma ascendente

            return $query->orderBy('id', 'asc');
        }
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
        $perPage = 15;

        if (request()->has('per_page')) {
            $rulesPerPage = [
                'per_page' => 'integer|min:2|max:100',
            ];

            Validator::validate(request()->all(), $rulesPerPage);

            $perPage = intval(request()->get('per_page'));
        }

        $paginate = TRUE;

        if (request()->has('paginate')) {
            $paginate = (request()->input('paginate') === 'true');
        }

        return $paginate
            ? $query->paginate($perPage)
                    ->appends(request()->all())
            : $query->get();
    }
}
