<?php
namespace MrJmpl3\LaravelRestfulHelper;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use Throwable;

abstract class RestfulHelper
{
    public Builder $queryBuilder;

    public Model $model;

    public bool $canFields;

    public bool $canFilters;

    public bool $canSorts;

    public bool $canPaginate;

    public array $structures;

    public ?array $structureFiltered = null;

    public ?array $transformers = null;

    public ?array $allowedFields = null;

    public ?array $allowedFilters = null;

    public ?array $allowedSorts = null;

    public ?string $fieldGroupName = null;

    /**
     * @throws Throwable
     */
    public function __construct(string|Model|Builder $subject)
    {
        if (\gettype($subject) === 'string') {
            $this->model = (new $subject());
            $this->queryBuilder = $this->model->newQuery();
        } elseif ($subject instanceof Builder) {
            $this->model = $subject->getModel();
            $this->queryBuilder = $subject;
        } else {
            $this->model = $subject;
            $this->queryBuilder = $subject->newQuery();
        }

        $this->canFields = Config::get('restful_helper.fields', true);
        $this->canFilters = Config::get('restful_helper.filters', true);
        $this->canSorts = Config::get('restful_helper.sorts', true);
        $this->canPaginate = Config::get('restful_helper.paginate', true);
        $this->structures = Config::get('restful_helper.structures', []);

        $this->calculateStructureFiltered();
        $this->calculateTransformers();
        $this->calculateAllowedFields();
        $this->calculateAllowedFilters();
        $this->calculateAllowedSorts();
        $this->calculateFieldGroupName();
    }

    abstract public function toModel(): Model|null;

    abstract public function toCollection(): Collection|LengthAwarePaginator;

    abstract public function existInFieldsRequest(string $key): bool;

    abstract public function getFieldsRequest(): \Illuminate\Support\Collection;

    /**
     * @throws Throwable
     */
    public function getTransformedColumn(string $originalColumn): string
    {
        throw_if( ! $this->existsOriginalColumn($originalColumn), sprintf('Original column of ApiRestHelper missing: %s', $originalColumn));

        return $this->transformers[$originalColumn];
    }

    /**
     * @throws Throwable
     */
    public function getOriginalColumn(string $transformedColumn): string
    {
        throw_if( ! $this->existsTransformedColumn($transformedColumn), sprintf('Original column of ApiRestHelper missing: %s', $transformedColumn));

        return array_search($transformedColumn, $this->transformers, true);
    }

    /**
     * @throws Throwable
     */
    public function existsOriginalColumn(string $key): bool
    {
        return \array_key_exists($key, $this->transformers);
    }

    /**
     * @throws Throwable
     */
    public function existsTransformedColumn(string $value): bool
    {
        return \in_array($value, $this->transformers);
    }

    public function calculateStructureFiltered(): void
    {
        $this->structureFiltered = collect($this->structures)
            ->where('model', \get_class($this->model))
            ->pluck('data')
            ->flatten(1)
            ->filter(fn ($item) => \in_array(Route::currentRouteName(), $item['routes'], true))
            ->first() ?? [];
    }

    /**
     * @throws Throwable
     */
    public function calculateTransformers(): void
    {
        if (\count($this->structureFiltered) === 0) {
            $this->transformers = [];

            return;
        }

        if ( ! \array_key_exists('transformer', $this->structureFiltered)) {
            $this->transformers = [];

            return;
        }

        throw_if(
            ! property_exists($this->model, $this->structureFiltered['transformer']),
            sprintf('Property of ApiRestHelper missing: %s in class %s', $this->structureFiltered['transformer'], $this->model)
        );

        $this->transformers = $this->model->{$this->structureFiltered['transformer']};
    }

    /**
     * @throws Throwable
     */
    public function calculateAllowedFields(): void
    {
        if (\count($this->structureFiltered) === 0) {
            $this->transformers = [];

            return;
        }

        if ( ! \array_key_exists('allowedFields', $this->structureFiltered)) {
            $this->allowedFields = [];

            return;
        }

        throw_if(
            ! property_exists($this->model, $this->structureFiltered['allowedFields']),
            sprintf('Property of ApiRestHelper missing: %s in class %s', $this->structureFiltered['allowedFields'], $this->model)
        );

        $this->allowedFields = $this->model->{$this->structureFiltered['allowedFields']};
    }

    /**
     * @throws Throwable
     */
    public function calculateAllowedFilters(): void
    {
        if (\count($this->structureFiltered) === 0) {
            $this->transformers = [];

            return;
        }

        if ( ! \array_key_exists('allowedFilters', $this->structureFiltered)) {
            $this->allowedFilters = [];

            return;
        }

        throw_if(
            ! property_exists($this->model, $this->structureFiltered['allowedFilters']),
            sprintf('Property of ApiRestHelper missing: %s in class %s', $this->structureFiltered['allowedFilters'], $this->model)
        );

        $this->allowedFilters = $this->model->{$this->structureFiltered['allowedFilters']};
    }

    /**
     * @throws Throwable
     */
    public function calculateAllowedSorts(): void
    {
        if (\count($this->structureFiltered) === 0) {
            $this->transformers = [];

            return;
        }

        if ( ! \array_key_exists('allowedSorts', $this->structureFiltered)) {
            $this->allowedSorts = [];

            return;
        }

        throw_if(
            ! property_exists($this->model, $this->structureFiltered['allowedSorts']),
            sprintf('Property of ApiRestHelper missing: %s in class %s', $this->structureFiltered['allowedSorts'], $this->model)
        );

        $this->allowedSorts = $this->model->{$this->structureFiltered['allowedSorts']};
    }

    /**
     * @throws Throwable
     */
    public function calculateFieldGroupName(): void
    {
        if (\count($this->structureFiltered) === 0) {
            $this->fieldGroupName = null;

            return;
        }

        if ( ! \array_key_exists('fieldGroupName', $this->structureFiltered)) {
            $this->fieldGroupName = null;

            return;
        }

        throw_if(
            ! property_exists($this->model, $this->structureFiltered['fieldGroupName']),
            sprintf('Property of ApiRestHelper missing: %s in class %s', $this->structureFiltered['fieldGroupName'], $this->model)
        );

        $this->fieldGroupName = $this->model->{$this->structureFiltered['fieldGroupName']};
    }
}
