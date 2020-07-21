<?php
/**
 * Copyright (c) 2020.
 * Archivo desarrollado por Jose Manuel Casani Guerra bajo el pseudonimo de MrJmpl3.
 *
 * Email: jmpl3.soporte@gmail.com
 * Twitter: @MrJmpl3
 * Pagina Web: https://mrjmpl3-official.es
 */
namespace MrJmpl3\LaravelRestfulHelper\Classes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use MrJmpl3\LaravelRestfulHelper\Classes\Request\Builder as RequestBuilder;
use MrJmpl3\LaravelRestfulHelper\Classes\Request\Model as RequestModel;

class ApiRestHelper
{
    /**
     * @var \MrJmpl3\LaravelRestfulHelper\Classes\Request\Request
     */
    private $request;

    /**
     * @var array
     */
    private $transformers;

    /**
     * @var array
     */
    private $excludeFilter;

    /**
     * @var array
     */
    private $acceptRelations;

    /**
     * @var bool
     */
    private $executeFields;

    /**
     * @var bool
     */
    private $executeFilter;

    /**
     * @var bool
     */
    private $executeSorts;

    /**
     * @var bool
     */
    private $executePaginate;

    /**
     * Constructor.
     *
     * @param mixed $modelOrBuilder
     */
    public function __construct($modelOrBuilder)
    {
        if ($modelOrBuilder instanceof Model) {
            $this->request = new RequestModel($modelOrBuilder);
        } elseif ($modelOrBuilder instanceof Builder) {
            $this->request = new RequestBuilder($modelOrBuilder);
        }

        $this->transformers = $this->request->getTransformers();
        $this->excludeFilter = $this->request->getExcludeFilter();
        $this->acceptRelations = $this->request->getAcceptRelations();

        $this->executeFields = Config::get('restful_helper.fields', true);
        $this->executeFilter = Config::get('restful_helper.filters', true);
        $this->executeSorts = Config::get('restful_helper.sorts', true);
        $this->executePaginate = Config::get('restful_helper.paginate', true);
    }

    /*
     * --------------------------------------------------------------------------
     * Setters
     * --------------------------------------------------------------------------
     */

    /**
     * @param array $transformers
     */
    public function setTransformers(array $transformers): void
    {
        $this->transformers = $transformers;
    }

    /**
     * @param array $excludeFilter
     */
    public function setExcludeFilter(array $excludeFilter): void
    {
        $this->excludeFilter = $excludeFilter;
    }

    /**
     * @param array $acceptRelations
     */
    public function setAcceptRelations(array $acceptRelations): void
    {
        $this->acceptRelations = $acceptRelations;
    }

    /**
     * @param bool $executeFields
     */
    public function setExecuteFields(bool $executeFields): void
    {
        $this->executeFields = $executeFields;
    }

    /**
     * @param bool $executeFilter
     */
    public function setExecuteFilter(bool $executeFilter): void
    {
        $this->executeFilter = $executeFilter;
    }

    /**
     * @param bool $executeSorts
     */
    public function setExecuteSorts(bool $executeSorts): void
    {
        $this->executeSorts = $executeSorts;
    }

    /**
     * @param bool $executePaginate
     */
    public function setExecutePaginate(bool $executePaginate): void
    {
        $this->executePaginate = $executePaginate;
    }

    /*
     * --------------------------------------------------------------------------
     * Public functions to developers
     * --------------------------------------------------------------------------
     */

    /**
     * Check if the key exist in transformer and return the value transformed,
     * if don't exists return the key.
     *
     * @param string $key
     *
     * @return string
     */
    public function getKeyTransformed(string $key): string
    {
        if (array_key_exists($key, $this->transformers)) {
            return $this->transformers[$key];
        }

        return $key;
    }

    /**
     * Returns the fields of the request in array
     * Example: ["column1", "column2", "column3", "column4"].
     *
     * @return array
     */
    public function getFieldsRequest(): array
    {
        $fields = [];

        if (request()->has('fields')) {
            $fields = explode(',', request()->get('fields'));
        }

        return $fields;
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function existInFieldsRequest(string $key): bool
    {
        return in_array($key, $this->getFieldsRequest(), true);
    }

    /**
     * Returns the filters of the request in an array associative
     * Example: ["column": "value", "column2": "value2"].
     *
     * @return array
     */
    public function getFiltersRequest(): array
    {
        $requests = request()->all();

        $filters = [];

        foreach ($requests as $key => $value) {
            if ($key !== 'sort' && $key !== 'fields' && $key !== 'embed' && $key !== 'paginate') {
                $filters[$key] = $value;
            }
        }

        return $filters;
    }

    /**
     * Return the sorts of the request
     * Example: ["column1": "desc", "column2": "asc"].
     *
     * @return array
     */
    public function getSortsRequest(): array
    {
        $sorts = [];

        if (request()->has('sort')) {
            $querySorts = explode(',', request()->get('sort'));

            foreach ($querySorts as $querySort) {
                $lenQuerySort = strlen($querySort);

                if ($lenQuerySort > 0) {
                    if (strpos($querySort, '-') === 0) {
                        $sorts[substr($querySort, 1, $lenQuerySort - 1)] = 'desc';
                    } else {
                        $sorts[$querySort] = 'asc';
                    }
                }
            }
        }

        return $sorts;
    }

    /**
     * Return the embed of the request.
     *
     * @return array
     */
    public function getEmbedRequest(): array
    {
        $embedRelations = [];

        if (request()->has('embed')) {
            $embedRelations = explode(',', request()->get('embed'));
        }

        $embed = [];

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
     * @param string $relationKey
     * @param string $key
     *
     * @return bool
     */
    public function existInEmbedFieldRequest(string $relationKey, string $key): bool
    {
        $embed = $this->getEmbedRequest();

        if (!array_key_exists($relationKey, $embed)) {
            return false;
        }

        $embedPerRelation = $embed[$relationKey];

        return in_array($key, $embedPerRelation, false);
    }

    /**
     * Returns the fields of the request but was used to the select query.
     *
     * @return array
     */
    public function getFields(): array
    {
        $fields = $this->getFieldsRequest();
        $attributes = $this->getAttributesOfModel();

        $fieldsTransformed = [];

        foreach ($fields as $field) {
            $attribute = in_array($field, array_values($this->transformers), true) ?
                array_search($field, $this->transformers, true) : $field;

            if (in_array($attribute, $attributes, true)) {
                $fieldsTransformed[] = $attribute;
            }
        }

        return $fieldsTransformed;
    }

    /**
     * Check if key exists in fields when the fields are empty, return true because mean '*' in SQL.
     *
     * @param string $key
     *
     * @return bool
     */
    public function existInFields(string $key): bool
    {
        $fields = $this->getFields();

        if (!empty($fields)) {
            return in_array($key, $fields, true);
        }

        return true;
    }

    /**
     * Returns the filters of the request but was transformed to original value.
     *
     * @return array
     */
    public function getFilters(): array
    {
        $filters = $this->getFiltersRequest();
        $attributes = $this->getAttributesOfModel();

        $filtersTransformed = [];

        foreach ($filters as $key => $value) {
            $attribute = in_array($key, array_values($this->transformers), true) ?
                array_search($key, $this->transformers, false) : $key;

            if (
                in_array($attribute, $attributes, false) &&
                !in_array($attribute, $this->excludeFilter, true)
            ) {
                $filtersTransformed = Arr::add($filtersTransformed, $attribute, $value);
            }
        }

        return $filtersTransformed;
    }

    /**
     * Returns the sorts of the request but was transformed to original value.
     *
     * @return array
     */
    public function getSorts(): array
    {
        $sorts = $this->getSortsRequest();

        $attributes = $this->getAttributesOfModel();

        $convertedSorts = [];

        foreach ($sorts as $key => $value) {
            $attribute = in_array($key, array_values($this->transformers), true) ?
                array_search($key, $this->transformers, false) : $key;

            if (in_array($attribute, $attributes, true)) {
                $convertedSorts = Arr::add($convertedSorts, $attribute, $value);
            }
        }

        return $convertedSorts;
    }

    /**
     * @return array
     */
    public function getPaginate(): array
    {
        $perPage = 15;

        if (request()->has('per_page')) {
            $rulesPerPage = [
                'per_page' => 'integer|min:2|max:200',
            ];

            try {
                Validator::validate(request()->all(), $rulesPerPage);
                $perPage = (int) request()->get('per_page');
            } catch (ValidationException $e) {
            }
        }

        $paginate = true;

        if (request()->has('paginate')) {
            $paginate = (request()->input('paginate') === 'true');
        }

        return [
            'paginate' => $paginate,
            'per_page' => $perPage,
        ];
    }

    /**
     * Returns the embed of the request but was transformed to original value.
     *
     * @return array
     */
    public function getEmbed(): array
    {
        $embed = $this->getEmbedRequest();
        $model = $this->request->getModel();

        $embedValidated = [];

        foreach ($embed as $key => $val) {
            if (method_exists($model, $key) && in_array($key, $this->acceptRelations, true)) {
                $embedValidated[$key] = $val;
            }
        }

        return $embedValidated;
    }

    /**
     * Returns a portion of the embed of the request but was transformed to original value.
     *
     * @param string $relationKey
     *
     * @return array
     */
    public function getEmbedField(string $relationKey): array
    {
        $embed = $this->getEmbed();
        $fieldsFromEmbed = Arr::get($embed, $relationKey, []);

        $attributes = $this->getAttributesOfModel();

        $fieldsTransformers = [];

        foreach ($fieldsFromEmbed as $fieldFromEmbed) {
            $attribute = in_array($fieldFromEmbed, array_values($this->transformers), true) ?
                array_search($fieldFromEmbed, $this->transformers, true) : $fieldFromEmbed;

            if (in_array($attribute, $attributes, true)) {
                $fieldsTransformers[] = $attribute;
            }
        }

        return $fieldsTransformers;
    }

    /**
     * Check if key exists in fields of embed when the fields are empty, return true because mean '*' in SQL.
     *
     * @param $relationKey
     * @param $key
     *
     * @return bool
     */
    public function existInEmbedField($relationKey, $key): bool
    {
        if (!array_key_exists($relationKey, $this->getEmbed())) {
            return false;
        }

        $embedPerRelation = $this->getEmbedField($relationKey);

        if (!empty($embedPerRelation)) {
            return in_array($key, $embedPerRelation, false);
        }

        return true;
    }

    /**
     * @return null|\Illuminate\Database\Eloquent\Model
     */
    public function toModel(): ?Model
    {
        $query = $this->request->getBuilder();

        if ($this->executeFields) {
            $query = $this->apiFields($query);
        }

        if ($this->executeFilter) {
            $query = $this->apiFilter($query);
        }

        return $query->first();
    }

    /**
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Builder
     */
    public function toCollection()
    {
        $query = $this->request->getBuilder();

        if ($this->executeFields) {
            $query = $this->apiFields($query);
        }

        if ($this->executeFilter) {
            $query = $this->apiFilter($query);
        }

        if ($this->executeSorts) {
            $query = $this->apiSort($query);
        }

        if ($this->executePaginate) {
            $query = $this->apiPaginate($query);
        }

        return $query;
    }

    /*
     * --------------------------------------------------------------------------
     * Private functions to processing
     * --------------------------------------------------------------------------
     */

    /**
     * @return array
     */
    private function getAttributesOfModel(): array
    {
        $model = $this->request->getModel();
        $columns = Schema::getColumnListing($model->getTable());

        return array_diff($columns, $model->getHidden());
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function apiFields($query)
    {
        $convertedFields = $this->getFields();

        if (count($convertedFields) > 0) {
            $query = $query->select($convertedFields);
        }

        return $query;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function apiFilter($query)
    {
        $model = $this->request->getModel();
        $casts = $model->getCasts();

        $convertedFilters = $this->getFilters();

        foreach ($convertedFilters as $key => $value) {
            if ($value === '') {
                $query = $query->where($key, '=');
            } else {
                switch ($casts[$key]) {
                    case 'int':
                    case 'integer':
                        $query = $query->where($key, '=', (int) $value);

                        break;
                    case 'real':
                    case 'float':
                    case 'double':
                        $query = $query->where($key, '=', (float) $value);

                        break;
                    case 'string':
                        $query = $query->where($key, '=', (string) $value);

                        break;
                    case 'bool':
                    case 'boolean':
                        $query = $query->where($key, '=', (bool) $value);

                        break;
                    case 'date':
                        $query = $query->where($key, '=', $value);

                        break;
                }
            }
        }

        return $query;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function apiSort($query)
    {
        $convertedSorts = $this->getSorts();

        foreach ($convertedSorts as $key => $value) {
            $query = $query->orderBy($key, $value);
        }

        return $query;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection
     */
    private function apiPaginate($query)
    {
        $paginateArr = $this->getPaginate();

        if (Arr::get($paginateArr, 'paginate', true)) {
            return $query->paginate(Arr::get($paginateArr, 'per_page', 15))->appends(request()->all());
        }

        return $query->get();
    }
}
