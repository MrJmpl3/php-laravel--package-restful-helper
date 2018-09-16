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
     * @param \Illuminate\Database\Eloquent\Model $model
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function executeApiResponse($model)
    {
        $query = $model;

        if (config('restful_helper.fields')) {
            $query = $this->apiFields($query, $model);
        }

        if (config('restful_helper.filters')) {
            $query = $this->apiFilter($query, $model);
        }

        if (config('restful_helper.sorts')) {
            $query = $this->apiSort($query, $model);
        }

        if (config('restful_helper.paginate')) {
            $query = $this->apiPaginate($query);
        }

        return $query;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $model
     *
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Builder
     */
    protected function apiFieldsOnlyModel($model)
    {
        if (request()->has('fields')) {
            $columns = Schema::getColumnListing($model->getTable());
            $queryFields = explode(',', request()->get('fields'));

            $selectColumns = [];

            foreach ($columns as $column) {
                if (isset($model->transforms[$column])) {
                    if (in_array($model->transforms[$column], $queryFields)) {
                        $selectColumns[] = $column;
                    }
                } else {
                    if (in_array($column, $queryFields)) {
                        $selectColumns[] = $column;
                    }
                }
            }

            if (count($selectColumns) > 0) {
                $model = $model->select($selectColumns);
            }
        }

        return $model;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Builder $query
     * @param \Illuminate\Database\Eloquent\Model                                       $model
     *
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Builder
     */
    private function apiFields($query, $model)
    {
        if (request()->has('fields')) {
            $columns = Schema::getColumnListing($model->getTable());
            $queryFields = explode(',', request()->get('fields'));

            $selectColumns = [];

            foreach ($columns as $column) {
                if (isset($model->transforms[$column])) {
                    if (in_array($model->transforms[$column], $queryFields)) {
                        $selectColumns[] = $column;
                    }
                } else {
                    if (in_array($column, $queryFields)) {
                        $selectColumns[] = $column;
                    }
                }
            }

            if (count($selectColumns) > 0) {
                $query = $query->select($selectColumns);
            }
        }

        return $query;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Builder $query
     * @param \Illuminate\Database\Eloquent\Model                                       $model
     *
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Builder
     */
    private function apiFilter($query, $model)
    {
        $columns = Schema::getColumnListing($model->getTable());
        $casts = $model->getCasts();

        foreach ($columns as $column) {
            if (isset($model->transforms[$column])) {
                if (request()->has($model->transforms[$column])) {
                    if (request()->input($model->transforms[$column]) === '') {
                        $query = $query->where($column, '=', null);
                    } else {
                        switch ($casts[$column]) {
                            case 'int':
                            case 'integer':
                                $query = $query->where($column, '=', intval(request()->input($model->transforms[$column])));
                                break;
                            case 'real':
                            case 'float':
                            case 'double':
                                $query = $query->where($column, '=', floatval(request()->input($model->transforms[$column])));
                                break;
                            case 'string':
                                $query = $query->where($column, '=', strval(request()->input($model->transforms[$column])));
                                break;
                            case 'bool':
                            case 'boolean':
                                $query = $query->where($column, '=', boolval(request()->input($model->transforms[$column])));
                                break;
                            case 'date':
                            case 'datetime':
                                $query = $query->where($column, '=', Carbon::createFromFormat('d/m/Y', request()->input($model->transforms[$column]), 'America/Lima')->format('Y-m-d'));
                                break;
                        }
                    }
                }
            } else {
                if (request()->has($column)) {
                    if (request()->input($column) === '') {
                        $query = $query->where($column, '=', null);
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
                                $query = $query->where($column, '=', Carbon::createFromFormat('d/m/Y', request()->input($column), 'America/Lima')->format('Y-m-d'));
                                break;
                        }
                    }
                }
            }
        }

        return $query;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Builder $query
     * @param \Illuminate\Database\Eloquent\Model                                       $model
     *
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Builder
     */
    private function apiSort($query, $model)
    {
        if (request()->has('sort')) {
            $querySortBy = explode(',', request()->get('sort'));
            $columns = Schema::getColumnListing($model->getTable());

            foreach ($columns as $column) {
                if (isset($model->transforms[$column])) {
                    if (in_array($model->transforms[$column], array_values($querySortBy))) {
                        $query = $query->orderBy($column, 'asc');
                    } elseif (in_array('-'.$model->transforms[$column], array_values($querySortBy))) {
                        $query = $query->orderBy($column, 'desc');
                    }
                } else {
                    if (in_array($column, array_values($querySortBy))) {
                        $query = $query->orderBy($column, 'asc');
                    } elseif (in_array('-'.$column, array_values($querySortBy))) {
                        $query = $query->orderBy($column, 'desc');
                    }
                }
            }

            return $query;
        } else {
            return $query->orderBy('id', 'asc');
        }
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Model
     * @throws \Illuminate\Validation\ValidationException
     */
    private function apiPaginate($query)
    {
        $perPage = 15;

        if (request()->has('per_page')) {
            $rulesPerPage = [
                'per_page' => 'integer|min:2|max:50',
            ];

            Validator::validate(request()->all(), $rulesPerPage);

            $perPage = intval(request()->get('per_page'));
        }

        $paginate = true;

        if (request()->has('paginate')) {
            $paginate = (request()->input('paginate') === 'true');
        }

        return $paginate
            ? $query->paginate($perPage)->appends(request()->all())
            : $query->get();
    }

    /**
     * @param        $model
     * @param string $originalValue
     *
     * @return bool
     */
    protected function existsInApiFields($model, string $originalValue)
    {
        $queryFields = (request()->has('fields'))
            ? explode(',', request()->get('fields'))
            : [];

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
