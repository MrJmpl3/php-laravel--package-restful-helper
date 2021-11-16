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

    public ?array $structureFiltered = null;

    public ?array $transformers = null;

    public ?array $allowedFields = null;

    public ?array $allowedFilters = null;

    public ?array $allowedSorts = null;

    public ?string $fieldGroupName = null;

    protected bool $canFields;

    protected bool $canFilters;

    protected bool $canSorts;

    protected bool $canPaginate;

    protected array $structures;

    /**
     * @throws Throwable
     */
    public function __construct(string|Model|Builder $subject)
    {
        if (\gettype($subject) === 'string') {
            $this->model = (new $subject());
            $this->queryBuilder = $this->model->newQuery();
        } elseif ($subject instanceof Builder) {
            $this->queryBuilder = $subject;
            $this->model = $subject->getModel();
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

    /**
     * @throws Throwable
     */
    public function getKeyTransformed(string $key): string
    {
        throw_if( ! $this->existsKeyTransformed($key), "Key of transform data of ApiRestHelper missing: {$key}");

        return $this->transformers[$key];
    }

    /**
     * @throws Throwable
     */
    public function getValueTransformed(string $value): string
    {
        throw_if( ! $this->existsValueTransformed($value), "Value of transform data of ApiRestHelper missing: {$value}");

        return array_search($value, $this->transformers, true);
    }

    /**
     * @throws Throwable
     */
    public function existsKeyTransformed(string $key): bool
    {
        return \array_key_exists($key, $this->transformers);
    }

    /**
     * @throws Throwable
     */
    public function existsValueTransformed(string $value): bool
    {
        return \in_array($value, $this->transformers);
    }

    public function calculateStructureFiltered(): void
    {
        $this->structureFiltered = collect($this->structures)
            ->where('model', \get_class($this->model))
            ->pluck('data')
            ->flatten(1)
            ->filter(fn ($item) => array_search(Route::currentRouteName(), $item['routes'], true) !== false)
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
            "Property of ApiRestHelper missing: {$this->structureFiltered['transformer']} in class {$this->model}"
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
            "Property of ApiRestHelper missing: {$this->structureFiltered['allowedFields']} in class {$this->model}"
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
            "Property of ApiRestHelper missing: {$this->structureFiltered['allowedFilters']} in class {$this->model}"
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
            "Property of ApiRestHelper missing: {$this->structureFiltered['allowedSorts']} in class {$this->model}"
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
            "Property of ApiRestHelper missing: {$this->structureFiltered['fieldGroupName']} in class {$this->model}"
        );

        $this->fieldGroupName = $this->model->{$this->structureFiltered['fieldGroupName']};
    }
}
