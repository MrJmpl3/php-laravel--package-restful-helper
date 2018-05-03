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
     * Sirve para seleccionar datos / Select some fields to response
     *
     * Ejemplo / Example: /product?fields=value1,value2,value3,value4
     * En el mundo real / In RealWorld: /product?fields=id,name
     *
     * @param \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Builder $model
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model
     */
    protected function apiFields($model)
    {
        /**
         * Si existe la consulta de fields
         */
        if (request()->has('fields')) {
            /**
             * Obtengo las columnas de la tabla del modelo
             */
            $columns = Schema::getColumnListing($model->getModel()->getTable());

            /**
             * Obtengo la linea de consulta de fields
             */
            $queryFields = explode(',', request()->get('fields'));

            /**
             * Arreglo de Select
             */
            $selectColumns = [];

            /**
             * Recorro las columnas
             */
            foreach ($columns as $column) {
                /**
                 * Verifico si la columna esta asignada en el transform
                 */
                if (isset($model->getModel()->transforms[$column])) {
                    /**
                     * Verifico si la columna modificada esta en la consulta
                     */
                    if (in_array($model->getModel()->transforms[$column], $queryFields)) {
                        $selectColumns[] = $column;
                    }
                } else {
                    /**
                     * Verifico si la columna esta en la consulta
                     */
                    if (in_array($column, $queryFields)) {
                        $selectColumns[] = $column;
                    }
                }
            }

            /**
             * Selecciono segun la consulta solo si el arreglo de Select no esta vacio
             */
            if (count($selectColumns) > 0) {
                $model = $model->select($selectColumns);
            }
        }

        /**
         * Retorno el modelo
         */
        return $model;
    }

    /**
     * Sirve para filtrar datos / Filter some fields with data to response
     *
     * Ejemplo / Example: /product?value1=data1&value2=data2
     * En el mundo real / In RealWorld: /product?id=1&name=test
     *
     * @param \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Builder $model
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model
     */
    protected function apiFilter($model)
    {
        /**
         * Obtengo las columnas de la tabla del modelo
         */
        $columns = Schema::getColumnListing($model->getTable());

        /**
         * Obtengo el tipo de variable de cada columna
         */
        $casts = $model->getCasts();

        /**
         * Recorro las columnas
         */
        foreach ($columns as $column) {
            /**
             * Verifico si la columna esta asignada en el transform
             */
            if (isset($model->getModel()->transforms[$column])) {
                /**
                 * Verifico si la columna modificado existe en la peticion
                 */
                if (request()->has($model->getModel()->transforms[$column])) {
                    /**
                     * Ejecuto el where segun el casteo y dato enviado
                     */
                    if (request()->input($model->getModel()->transforms[$column]) === '') {
                        $model = $model->where($column, '=', null);
                    } else {
                        switch ($casts[$column]) {
                            case 'int':
                            case 'integer':
                                $model = $model->where($column, '=', intval(request()->input($model->getModel()->transforms[$column])));
                                break;
                            case 'real':
                            case 'float':
                            case 'double':
                                $model = $model->where($column, '=', floatval(request()->input($model->getModel()->transforms[$column])));
                                break;
                            case 'string':
                                $model = $model->where($column, '=', strval(request()->input($model->getModel()->transforms[$column])));
                                break;
                            case 'bool':
                            case 'boolean':
                                $model = $model->where($column, '=', boolval(request()->input($model->getModel()->transforms[$column])));
                                break;
                            case 'date':
                            case 'datetime':
                                $model = $model->where($column, '=', Carbon::createFromFormat('d/m/Y', request()->input($model->getModel()->transforms[$column]), 'America/Lima')->format('Y-m-d'));
                                break;
                        }
                    }
                }
            } else {
                /**
                 * Ejecuto el where segun el casteo y dato enviado
                 */
                if (request()->has($column)) {
                    if (request()->input($column) === '') {
                        $model = $model->where($column, '=', null);
                    } else {
                        switch ($casts[$column]) {
                            case 'int':
                            case 'integer':
                                $model = $model->where($column, '=', intval(request()->input($column)));
                                break;
                            case 'real':
                            case 'float':
                            case 'double':
                                $model = $model->where($column, '=', floatval(request()->input($column)));
                                break;
                            case 'string':
                                $model = $model->where($column, '=', strval(request()->input($column)));
                                break;
                            case 'bool':
                            case 'boolean':
                                $model = $model->where($column, '=', boolval(request()->input($column)));
                                break;
                            case 'date':
                            case 'datetime':
                                $model = $model->where($column, '=', Carbon::createFromFormat('d/m/Y', request()->input($column), 'America/Lima')->format('Y-m-d'));
                                break;
                        }
                    }
                }
            }
        }

        return $model;
    }

    /**
     * Sirve para ordenar datos / Sort some fields to response
     *
     * Ejemplo / Example : /product?sort=-column1,column2
     * En el mundo real / In RealWorld: /product?sort=-id,name
     *
     * Con prefijo negativo / With negative prefix = desc
     * Sin prefijo negativo / Without negative prefix = asc
     *
     * @param \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Builder $model
     * @return \Illuminate\Database\Eloquent\Model
     */
    protected function apiSort($model)
    {
        /**
         * Si existe la consulta de sortBy
         */
        if (request()->has('sort')) {
            /**
             * Obtengo la linea de consulta de sortBy
             */
            $querySortBy = explode(',', request()->get('sort'));

            /**
             * Obtengo las columnas de la tabla del modelo
             */
            $columns = Schema::getColumnListing($model->getTable());

            /**
             * Recorro las columnas
             */
            foreach ($columns as $column) {
                /**
                 * Verifico si la columna esta asignada en el transform
                 */
                if (isset($model->getModel()->transforms[$column])) {
                    /**
                     * En caso de que existe una query de la columna modificada
                     * Caso aparte: En caso de que existe una query de la columna pero con un '-'
                     */
                    if (in_array($model->getModel()->transforms[$column], array_values($querySortBy))) {
                        $model = $model->orderBy($column, 'asc');
                    } elseif (in_array('-'.$model->getModel()->transforms[$column], array_values($querySortBy))) {
                        $model = $model->orderBy($column, 'desc');
                    }
                } else {
                    /**
                     * En caso de que existe una query de la columna
                     * Caso aparte: En caso de que existe una query de la columna pero con un '-'
                     */
                    if (in_array($column, array_values($querySortBy))) {
                        $model = $model->orderBy($column, 'asc');
                    } elseif (in_array('-'.$column, array_values($querySortBy))) {
                        $model = $model->orderBy($column, 'desc');
                    }
                }
            }

            /**
             * Retorno el modelo
             */
            return $model;
        } else {
            /**
             * Retorno el modelo con el orderBy basico
             */
            return $model->orderBy('id', 'asc');
        }
    }

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
     *
     * @param \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Builder $model
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Model
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function apiPaginate($model)
    {
        /**
         * Valor inicial de Paginas
         */
        $perPage = 15;

        /**
         * Si existe la consulta
         */
        if (request()->has('per_page')) {
            /**
             * Valido que este correcto el parametro
             */
            $rulesPerPage = [
                'per_page' => 'integer|min:2|max:50',
            ];

            Validator::validate(request()->all(), $rulesPerPage);

            /**
             * Asigno el nuevo valor
             */
            $perPage = intval(request()->get('per_page'));
        }

        /**
         * Valor inicial de Paginacion
         */
        $paginate = true;

        /**
         * Si existe la consulta
         */
        if (request()->has('paginate')) {
            /**
             * Asigno el nuevo valor
             */
            $paginate = (request()->input('paginate') === 'true');
        }

        /**
         * Retorno el modelo final con/sin la paginacion conservando las demas queries en meta - links
         */
        return $paginate ? $model->paginate($perPage)->appends(request()->all()) : $model->get();
    }

    /**
     * Execute Api Response
     *
     * @param $model
     * @return mixed
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function executeApiResponse($model)
    {
        if (config('restful_helper.filters')) {
            $model = $this->apiFilter($model);
        }

        if (config('restful_helper.sorts')) {
            $model = $this->apiSort($model);
        }

        if (config('restful_helper.fields')) {
            $model = $this->apiFields($model);
        }

        if (config('restful_helper.paginate')) {
            $model = $this->apiPaginate($model);
        }

        return $model;
    }

    /**
     * @param $model
     * @param string $originalValue
     * @return bool
     */
    protected function existsInApiFields($model, string $originalValue)
    {
        $queryFields = (request()->has('fields')) ? explode(',', request()->get('fields')) : [];

        if (count($queryFields) === 0) {
            return true;
        }

        if (isset($model->transforms)) {
            if (isset($model->transforms[$originalValue])) {
                if (in_array($model->transforms[$originalValue], $queryFields)) {
                    return true;
                } else {
                    return false;
                }
            }
        }

        if (in_array($originalValue, $queryFields)) {
            return true;
        } else {
            return false;
        }
    }
}
