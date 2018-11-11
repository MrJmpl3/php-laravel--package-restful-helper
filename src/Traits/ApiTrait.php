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
     * @param $model
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model
     */
    protected function executeApiResponseToRC($model)
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
     * @param \Illuminate\Database\Eloquent\Builder $builder
     *
     * @param                                       $blockFilter
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model
     */
    protected function executeApiResponseFromBuilderToRC($builder, $blockFilter)
    {
        $model = $builder->getModel();
        $query = $builder;

        if (config('restful_helper.fields')) {
            $query = $this->apiFields($query, $model);
        }

        if (config('restful_helper.filters')) {
            $query = $this->apiFilter($query, $model, $blockFilter);
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
    protected function executeApiResponseToResource($model)
    {
        if (request()->has('fields')) {
            $columns = Schema::getColumnListing($model->getTable());
            $queryFields = explode(',', request()->get('fields'));

            $selectColumns = [];

            foreach ($columns as $column) {
                if (isset($model->apiTransforms[$column])) {
                    if (in_array($model->apiTransforms[$column], $queryFields)) {
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
     * @param \Illuminate\Database\Eloquent\Builder $builder
     *
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model
     */
    protected function executeApiResponseFromBuilderToResource($builder)
    {
        if (request()->has('fields')) {
            $columns = Schema::getColumnListing($builder->getModel()->getTable());
            $queryFields = explode(',', request()->get('fields'));

            $selectColumns = [];

            foreach ($columns as $column) {
                if (isset($builder->getModel()->apiTransforms[$column])) {
                    if (in_array($builder->getModel()->apiTransforms[$column], $queryFields)) {
                        $selectColumns[] = $column;
                    }
                } else {
                    if (in_array($column, $queryFields)) {
                        $selectColumns[] = $column;
                    }
                }
            }

            if (count($selectColumns) > 0) {
                $builder = $builder->select($selectColumns);
            }
        }

        return $builder;
    }

    /**
     * @param \Illuminate\Http\Resources\Json\Resource $resource
     * @param bool                                     $forced
     *
     * @return array
     */
    protected function embed(Resource $resource, bool $forced = false)
    {
        $embedListOfRelations = [];
        if ($forced) {
            $embedListOfRelations = $resource->resource->apiAcceptRelations;
        } elseif (request()->has('embed')) {
            $embedListOfRelations = explode(',', request()->get('embed'));
        }

        $embedListOfSelectsFromRelations = [];
        foreach ($embedListOfRelations as $embedListOfRelation) {
            $parts = explode('.', $embedListOfRelation);

            if (count($parts) > 1) {
                $embedListOfSelectsFromRelations[$parts[0]][] = $parts[1];
            } else {
                $embedListOfSelectsFromRelations[$parts[0]] = [];
            }
        }

        $responsesEmbed = [];
        foreach ($embedListOfSelectsFromRelations as $key => $val) {
            if ($resource->resource->apiAcceptRelations !== null) {
                if (method_exists($resource->resource, $key) && (in_array($key, $resource->resource->apiAcceptRelations))) {
                    $responsesEmbed[$key] = $val;
                }
            }
        }

        return $responsesEmbed;
    }

    /**
     * @param $modelOrRelation
     * @param $arrayFields
     *
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    protected function apiFieldsFromArrayToResource($modelOrRelation, $arrayFields)
    {
        $model = null;

        if (get_class($modelOrRelation) === 'Illuminate\Database\Eloquent\Relations\BelongsTo' || get_class($modelOrRelation) === 'Illuminate\Database\Eloquent\Relations\HasOne') {
            /** @var \Illuminate\Database\Eloquent\Relations\BelongsTo $modelOrRelation */
            /** @var \Illuminate\Database\Eloquent\Model $model */

            $model = $modelOrRelation->getModel();
        } else {
            $model = $modelOrRelation;
        }

        /** @var \Illuminate\Database\Eloquent\Model $model */

        $selectColumns = [];
        $columns = Schema::getColumnListing($model->getTable());

        foreach ($columns as $column) {
            if (isset($model->apiTransforms[$column])) {
                if (in_array($model->apiTransforms[$column], $arrayFields)) {
                    $selectColumns[] = $column;
                }
            } else {
                if (in_array($column, $arrayFields)) {
                    $selectColumns[] = $column;
                }
            }
        }

        if (count($selectColumns) > 0) {
            return $modelOrRelation->select($selectColumns);
        } else {
            return $modelOrRelation;
        }
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
                if (isset($model->apiTransforms[$column])) {
                    if (in_array($model->apiTransforms[$column], $queryFields)) {
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
     * @param array                                                                     $blockFilter
     *
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Builder
     */
    private function apiFilter($query, $model, $blockFilter = [])
    {
        $columns = Schema::getColumnListing($model->getTable());
        $casts = $model->getCasts();

        foreach ($columns as $column) {
            if(!isset($model->apiExcludeFilter[$column]) && !isset($blockFilter[$column])) {
                if (isset($model->apiTransforms[$column])) {
                    if (request()->has($model->apiTransforms[$column])) {
                        if (request()->input($model->apiTransforms[$column]) === '') {
                            $query = $query->where($column, '=', null);
                        } else {
                            switch ($casts[$column]) {
                                case 'int':
                                case 'integer':
                                    $query = $query->where($column, '=', intval(request()->input($model->apiTransforms[$column])));
                                    break;
                                case 'real':
                                case 'float':
                                case 'double':
                                    $query = $query->where($column, '=', floatval(request()->input($model->apiTransforms[$column])));
                                    break;
                                case 'string':
                                    $query = $query->where($column, '=', strval(request()->input($model->apiTransforms[$column])));
                                    break;
                                case 'bool':
                                case 'boolean':
                                    $query = $query->where($column, '=', boolval(request()->input($model->apiTransforms[$column])));
                                    break;
                                case 'date':
                                case 'datetime':
                                    $query = $query->where($column, '=', Carbon::createFromFormat('d/m/Y', request()->input($model->apiTransforms[$column]), 'America/Lima')->format('Y-m-d'));
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
                if (isset($model->apiTransforms[$column])) {
                    if (in_array($model->apiTransforms[$column], array_values($querySortBy))) {
                        $query = $query->orderBy($column, 'asc');
                    } elseif (in_array('-'.$model->apiTransforms[$column], array_values($querySortBy))) {
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
}
