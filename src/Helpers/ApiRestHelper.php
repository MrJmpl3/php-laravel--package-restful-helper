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
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ApiRestHelper
{
    /**
     * Function to test if you install correctly the package
     *
     * @return string
     */
    public function test(): string
    {
        return 'ApiRestHelper is correct installed';
    }

    /**
     * Returns the fields of the request in array
     *
     * @return array
     */
    public function getQueryFields(): array
    {
        $fields = [];

        if (request()->has('fields')) {
            $fields = explode(',', request()->get('fields'));
        }

        /**
         * Return:
         *
         * [
         *  "column1",
         *  "column2",
         *  "column3",
         *  "column4"
         * ]
         */
        return $fields;
    }

    /**
     * Returns the validated fields of the request by comparing the apiTransforms of the model or the optional param
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param null|array                          $customApiTransforms
     *
     * @return array
     */
    public function getQueryFieldsValidated($model, ?array $customApiTransforms = null): array
    {
        // We get the fields of the request
        $fields = $this->getQueryFields();

        // Get the Builder table
        $table = $model->getTable();

        // Get the columns in the table
        $columns = Schema::getColumnListing($table);

        $apiTransforms = [];

        if ($customApiTransforms === null) {
            // We search if the property apiTransforms exists in the Model
            if (property_exists($model, 'apiTransforms')) {
                $apiTransforms = $model->apiTransforms;
            }
        } else {
            $apiTransforms = $customApiTransforms;
        }

        // Array where you will store the validated fields
        $validatedFields = [];

        // I go through each column of the petition
        foreach ($fields as $field) {

            /**
             * Verify if it exists in the apiTransforms
             * Get the actual column according to the apiTransforms
             * If you do not find is the apiTransforms it is understood that it is a real column
             */

            $validatedColumn = '';

            if (in_array($field, array_values($apiTransforms), false)) {
                $validatedColumn = array_search($field, $apiTransforms, false);
            } else {
                $validatedColumn = $field;
            }

            // Validate that the real column exists in the table
            if (in_array($validatedColumn, $columns, false)) {

                // Because if there is, we add it to the selection list
                $validatedFields[] = $validatedColumn;
            }
        }

        // Return the converted fields
        return $validatedFields;
    }

    /**
     * Returns the filters of the request in an array associative
     *
     * @return array
     */
    public function getQueryFilters(): array
    {
        // I get all the parameters of the request
        $requests = request()->all();

        // Array where the filters will be stored
        $filters = [];

        // I go through the parameters
        foreach ($requests as $key => $value) {
            // I verify that the request is not a reserved query
            if ($key !== 'sort' && $key !== 'fields' && $key !== 'embed' && $key !== 'paginate') {
                $filters[$key] = $value;
            }
        }

        /**
         * Return example
         *
         * [
         *  "column": "value",
         *  "column2": "value2"
         * ]
         *
         */

        return $filters;
    }

    /**
     * Returns the converted fields of the request by comparing the apiTransforms of the model or the optional param
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param null|array                          $customApiTransforms
     *
     * @return array
     */
    public function getQueryFiltersValidated($model, ?array $customApiTransforms = null): array
    {
        // We get the "filter" of the request
        $filters = $this->getQueryFilters();

        // I get the Builder table
        $table = $model->getTable();

        // I get the columns of the table
        $columns = Schema::getColumnListing($table);

        $apiTransforms = [];

        if ($customApiTransforms === null) {
            // We search if the property apiTransforms exists in the Model
            if (property_exists($model, 'apiTransforms')) {
                $apiTransforms = $model->apiTransforms;
            }
        } else {
            $apiTransforms = $customApiTransforms;
        }

        // We search if the property apiExcludeFilter exists in the Model
        $apiExcludeFilter = [];

        if (property_exists($model, 'apiExcludeFilter')) {
            $apiExcludeFilter = $model->apiExcludeFilter;
        }

        // Array where you store the converted fields
        $validatedFilters = [];

        // I go through each column of the petition
        foreach ($filters as $key => $value) {

            /**
             * I check if it exists in the "apiTransforms"
             * I get the actual column according to the "apiTransforms"
             * If you do not find is the "apiTransforms" it is understood that it is a real column
             */

            $validatedColumn = '';

            if (in_array($key, array_values($apiTransforms), false)) {
                $validatedColumn = array_search($key, $apiTransforms, false);
            } else {
                $validatedColumn = $key;
            }

            // We validate that the real column exists in the table and is not excluded
            if (in_array($validatedColumn, $columns, false) && !in_array($validatedColumn, $apiExcludeFilter, false)) {

                // Because if it exists we add it to the list of "filters"
                $validatedFilters = array_add($validatedFilters, $validatedColumn, $value);
            }
        }

        // Return the converted filters
        return $validatedFilters;
    }

    /**
     * @return array
     */
    public function getQuerySorts(): array
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

        /**
         * Return the sorts of the request
         *
         * [
         *  "column1": "desc",
         *  "column2": "asc"
         * ]
         */
        return $sorts;
    }

    /**
     * Returns the converted sorts of the request by comparing the apiTransforms of the model or the optional param
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param array|null                          $customApiTransforms
     *
     * @return array
     */
    public function getQuerySortsValidated($model, ?array $customApiTransforms = null): array
    {
        // Get the sorts of the request
        $sorts = $this->getQuerySorts();

        // Get the Builder table
        $table = $model->getTable();

        // Get the columns in the table $customApiTransforms
        $columns = Schema::getColumnListing($table);

        // We search if the property apiTransforms exists in the Model
        $apiTransforms = [];

        if ($customApiTransforms === null) {
            if (property_exists($model, 'apiTransforms')) {
                $apiTransforms = $model->apiTransforms;
            }
        } else {
            $apiTransforms = $customApiTransforms;
        }

        // Array where you store the converted fields
        $convertedSorts = [];

        // Go through each column of the petition
        foreach ($sorts as $key => $value) {

            /**
             * I check if it exists in the apiTransforms
             * I get the actual column according to the apiTransforms
             * If you do not find is the apiTransforms it is understood that it is a real column
             */

            $convertedColumn = '';

            if (in_array($key, array_values($apiTransforms), false)) {
                $convertedColumn = array_search($key, $apiTransforms, false);
            } else {
                $convertedColumn = $key;
            }

            // We validate that the actual column exists in the table
            if (in_array($convertedColumn, $columns, false)) {
                // Because if there is we add it to the list of sorts
                $convertedSorts = array_add($convertedSorts, $convertedColumn, $value);
            }
        }

        // Return the converted filters
        return $convertedSorts;
    }

    /**
     * @return array
     */
    public function getQueryPaginate(): array
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
     * @return array
     */
    public function getQueryEmbed(): array
    {
        // List of relations in the embed request
        $embedRelations = [];

        // The relations in the petition are separated by a "," if it is a forced action then it assigns all relationships
        if (request()->has('embed')) {
            $embedRelations = explode(',', request()->get('embed'));
        }

        // List of 'fields' of relationships
        $embed = [];

        // It separates relations using a "." and they are grouped according to their belongings in the relationship
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
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param bool                                $forced
     *
     * @return array
     */
    public function getQueryEmbedValidated($model, bool $forced = false): array
    {
        $embed = $this->getQueryEmbed();

        $apiAcceptRelations = [];

        // I verify that the property apiAcceptRelations exists
        if (property_exists($model, 'apiAcceptRelations')) {
            $apiAcceptRelations = $model->apiAcceptRelations;
        }

        if ($forced) {
            $embed = $apiAcceptRelations;
        }

        // List of relationships accepted by the system
        $embedValidated = [];

        foreach ($embed as $key => $val) {
            // Verify that the method exists in the "Model" and verify that the relationship obtained is allowed in the Model
            if (method_exists($model, $key) && in_array($key, $apiAcceptRelations, false)) {
                $embedValidated[$key] = $val;
            }
        }

        return $embedValidated;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $model Model to get the apiTransforms
     * @param string                              $key   Key to get the fields from embed
     * @param array|null                          $customApiTransforms
     *
     * @return array
     */
    public function getQueryEmbedFieldsValidate(Model $model, string $key, ?array $customApiTransforms = null): array
    {
        $embed = $this->getQueryEmbed();

        $fieldsFromEmbed = array_get($embed, $key, []);

        // Get the table from the Builder
        $table = $model->getTable();

        // Get the columns of the table
        $columns = Schema::getColumnListing($table);

        $apiTransforms = [];

        if ($customApiTransforms === null) {
            // We search if the property apiTransforms exists in the Model
            if (property_exists($model, 'apiTransforms')) {
                $apiTransforms = $model->apiTransforms;
            }
        } else {
            $apiTransforms = $customApiTransforms;
        }

        $validatedFields = [];

        foreach ($fieldsFromEmbed as $fieldFromEmbed) {

            /**
             * Check if it exists in the apiTransforms
             * Get the real column according to the apiTransforms
             * If you do not find is the apiTransforms it is understood that it is a real column
             */

            $validatedColumn = '';

            if (in_array($fieldFromEmbed, array_values($apiTransforms), false)) {
                $validatedColumn = array_search($fieldFromEmbed, $apiTransforms, false);
            } else {
                $validatedColumn = $fieldFromEmbed;
            }

            // We validate that the actual column exists in the table
            if (in_array($validatedColumn, $columns, false)) {
                // Because if there is, we add it to the selection list
                $validatedFields[] = $validatedColumn;
            }
        }

        return $validatedFields;
    }

    /**
     * It is used to convert a Model to an answer for a Resource Collection
     * The second argument is an array of tables in which we do not want to be filtered in the query
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param array                               $ignoreFilters
     * @param array|null                          $customApiTransformers
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model
     */
    public function responseToResourceCollection($model, array $ignoreFilters = [], ?array $customApiTransformers = null)
    {
        // The Model is copied to maintain an immutability
        $query = $model;

        if (config('restful_helper.fields')) {
            $query = $this->apiFields($query, $model, $customApiTransformers);
        }

        if (config('restful_helper.filters')) {
            $query = $this->apiFilter($query, $model, $ignoreFilters, $customApiTransformers);
        }

        if (config('restful_helper.sorts')) {
            $query = $this->apiSort($query, $model, $customApiTransformers);
        }

        if (config('restful_helper.paginate')) {
            $query = $this->apiPaginate($query);
        }

        return $query;
    }

    /**
     * It is used to convert a "Builder" to an answer for a "Resource Collection"
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param array                                 $ignoreFilters
     * @param array|null                            $customApiTransformers
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model
     */
    public function responseFromBuilderToResourceCollection($builder, $ignoreFilters = [], ?array $customApiTransformers = null)
    {
        $model = $builder->getModel();
        $query = $builder;

        if (config('restful_helper.fields')) {
            $query = $this->apiFields($query, $model, $customApiTransformers);
        }

        if (config('restful_helper.filters')) {
            $query = $this->apiFilter($query, $model, $ignoreFilters, $customApiTransformers);
        }

        if (config('restful_helper.sorts')) {
            $query = $this->apiSort($query, $model, $customApiTransformers);
        }

        if (config('restful_helper.paginate')) {
            $query = $this->apiPaginate($query);
        }

        return $query;
    }

    /**
     * It is used to convert a Builder to an answer for a Resource
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param null                                  $customApiTransforms
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function responseFromBuilderToResource($builder, $customApiTransforms = null): ?Model
    {
        $model = $builder->getModel();

        $fields = $this->getQueryFieldsValidated($model, $customApiTransforms);

        if (count($fields) > 0) {
            $builder = $builder->select($fields);
        }

        // Return a "Model" that will be used by the "Resource"
        return $builder->first();
    }

    /**
     * @param      $key
     *
     * @return bool
     */
    public function existInFields($key): bool
    {
        $fields = $this->getQueryFields();

        if (!empty($fields)) {
            return in_array($key, $fields, false);
        }

        return true;
    }

    /**
     * @param      $key
     * @param      $model
     *
     * @param null $customApiTransforms
     *
     * @return bool
     */
    public function existInFieldsValidated($key, $model, $customApiTransforms = null): bool
    {
        $fields = $this->getQueryFieldsValidated($model, $customApiTransforms);

        if (!empty($fields)) {
            return in_array($key, $fields, false);
        }

        return true;
    }

    /**
     * @param $keyRelation
     * @param $key
     *
     * @return bool
     */
    public function existInEmbedFields($keyRelation, $key): bool
    {
        if (!array_key_exists($keyRelation, $this->getQueryEmbed())) {
            return false;
        }

        $embedPerRelation = ($this->getQueryEmbed())[$keyRelation];

        if (!empty($embedPerRelation)) {
            return in_array($key, $embedPerRelation, false);
        }

        return true;
    }

    /**
     * @param            $keyRelation
     * @param            $key
     * @param            $model
     * @param array|null $customApiTransformers
     *
     * @return bool
     */
    public function existInEmbedFieldsValidated($keyRelation, $key, $model, ?array $customApiTransformers = null): bool
    {
        if (!array_key_exists($keyRelation, $this->getQueryEmbed())) {
            return false;
        }

        $embedPerRelation = $this->getQueryEmbedFieldsValidate($model, $keyRelation, $customApiTransformers);

        if (!empty($embedPerRelation)) {
            return in_array($key, $embedPerRelation, false);
        }

        return true;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Builder $query
     * @param \Illuminate\Database\Eloquent\Model                                       $model
     *
     * @param null                                                                      $customApiTransforms
     *
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Builder
     */
    private function apiFields($query, $model, $customApiTransforms = null)
    {
        $convertedFields = $this->getQueryFieldsValidated($model, $customApiTransforms);

        // Check if the real selections have content to avoid making an unnecessary query
        if (count($convertedFields) > 0) {
            $query = $query->select($convertedFields);
        }

        // Return a Builder
        return $query;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Builder $query
     * @param \Illuminate\Database\Eloquent\Model                                       $model
     * @param array                                                                     $ignoreFilters
     * @param null                                                                      $customApiTransforms
     *
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Builder
     */
    private function apiFilter($query, $model, array $ignoreFilters = [], $customApiTransforms = null)
    {
        $convertedFilters = $this->getQueryFiltersValidated($model, $customApiTransforms);

        // Get the casts of the Model
        $casts = $model->getCasts();

        foreach ($convertedFilters as $key => $value) {
            if (!array_key_exists($key, $ignoreFilters)) {
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
        }

        return $query;
    }

    /**
     * Process for the ordering of data
     *
     * @param \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Builder $query
     * @param \Illuminate\Database\Eloquent\Model                                       $model
     *
     * @param array|null                                                                $customApiTransforms
     *
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Builder
     */
    private function apiSort($query, $model, $customApiTransforms = null)
    {
        $convertedSorts = $this->getQuerySortsValidated($model, $customApiTransforms);

        foreach ($convertedSorts as $key => $value) {
            $query = $query->orderBy($key, $value);
        }

        return $query;
    }

    /**
     * Process for pagination
     *
     * @param \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Model
     */
    private function apiPaginate($query)
    {
        $paginateArr = $this->getQueryPaginate();

        if (array_get($paginateArr, 'paginate', true)) {
            return $query->paginate(array_get($paginateArr, 'per_page', 15))
                ->appends(request()->all());
        }

        return $query->get();
    }
}
