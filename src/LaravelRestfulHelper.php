<?php
namespace MrJmpl3\LaravelRestfulHelper;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;
use Throwable;

class LaravelRestfulHelper extends RestfulHelper
{
    private Request $customRequest;

    /**
     * @throws Throwable
     */
    public function toModel(): Model|null
    {
        $this->createCustomRequest();

        $laravelQueryBuilder = QueryBuilder::for($this->queryBuilder, $this->customRequest);

        if ($this->canFields) {
            $laravelQueryBuilder = $this->executeFields($laravelQueryBuilder);
        }

        if ($this->canFilters) {
            $laravelQueryBuilder = $this->executeFilter($laravelQueryBuilder);
        }

        return $laravelQueryBuilder->first();
    }

    /**
     * Get the fields of the request and get the original columns value using the transformers.
     */
    public function getFieldsRequest(): \Illuminate\Support\Collection
    {
        return collect($this->customRequest->get('fields'))
            ->map(
                fn ($item) => collect(explode(',', $item))
                    ->map(function ($item) {
                        if ($this->existsTransformedColumn($item)) {
                            return $this->getOriginalColumn($item);
                        }

                        return $item;
                    })
                    ->join(',')
            );
    }

    /**
     * @throws Throwable
     */
    public function toCollection(): Collection|LengthAwarePaginator
    {
        $this->createCustomRequest();

        $laravelQueryBuilder = QueryBuilder::for($this->queryBuilder, $this->customRequest);

        if ($this->canFields) {
            $laravelQueryBuilder = $this->executeFields($laravelQueryBuilder);
        }

        if ($this->canFilters) {
            $laravelQueryBuilder = $this->executeFilter($laravelQueryBuilder);
        }

        if ($this->canSorts) {
            $laravelQueryBuilder = $this->executeSort($laravelQueryBuilder);
        }

        return $this->executePaginate($laravelQueryBuilder);
    }

    public function existInFieldsRequest($key): bool
    {
        $fields = $this->getFieldsRequest()->get($this->model->getTable());

        if ($fields->isNotEmpty()) {
            return $fields->contains($key);
        }

        return true;
    }

    /**
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    private function createCustomRequest()
    {
        $this->customRequest = request()->duplicate();

        if ($this->fieldGroupName !== null) {
            $fieldCollection = collect($this->customRequest->get('fields'))
                ->mapWithKeys(function ($item, $key) {
                    if ($key === $this->fieldGroupName) {
                        return [$this->model->getTable() => $item];
                    }

                    return [$key => $item];
                });

            if ($fieldCollection->isNotEmpty()) {
                $this->customRequest = request()->duplicate(query: [
                    'fields' => $fieldCollection->all(),
                ]);
            }
        }
    }

    private function executeFields(QueryBuilder $queryBuilder): QueryBuilder
    {
        return $queryBuilder->allowedFields(explode(',', $this->getFieldsRequest()->get($this->model->getTable())));
    }

    private function executeFilter(QueryBuilder $queryBuilder): QueryBuilder
    {
        return $queryBuilder->allowedFilters(
            collect($this->customRequest->get('filter'))
                ->map(function ($item, $key) {
                    if ($this->existsTransformedColumn($key)) {
                        return [
                            'column' => $this->getOriginalColumn($key),
                            'alias' => $key,
                        ];
                    }

                    return [
                        'column' => $key,
                        'alias' => $key,
                    ];
                })
                ->values()
                ->filter(fn ($item) => \in_array($item['column'], $this->allowedFilters))
                ->map(fn ($item) => AllowedFilter::exact($item['alias'], $item['column']))
                ->all()
        );
    }

    private function executeSort(QueryBuilder $queryBuilder): QueryBuilder
    {
        return $queryBuilder->allowedSorts(
            collect(explode(',', $this->customRequest->get('sort')))
                ->filter(fn ($item) => ! empty($item))
                ->map(function ($item) {
                    if (Str::startsWith($item, '-')) {
                        $column = mb_substr($item, 1, mb_strlen($item));
                        $type = 'desc';
                    } else {
                        $column = $item;
                        $type = 'asc';
                    }

                    if ($this->existsTransformedColumn($column)) {
                        return [
                            'column' => $this->getOriginalColumn($column),
                            'alias' => $item,
                            'type' => $type,
                        ];
                    }

                    return [
                        'column' => $column,
                        'alias' => $item,
                        'type' => $type,
                    ];
                })
                ->filter(fn ($item) => \in_array($item['column'], $this->allowedSorts))
                ->map(fn ($item) => AllowedSort::field($item['alias'], $item['column']))
                ->all()
        );
    }

    private function executePaginate(QueryBuilder $queryBuilder): Collection|LengthAwarePaginator
    {
        if ( ! $this->canPaginate) {
            return $queryBuilder->get();
        }

        $paginate = ! $this->customRequest->has('paginate') || $this->customRequest->input('paginate') === 'true';

        return $paginate
            ? $queryBuilder->jsonPaginate()
            : $queryBuilder->get();
    }
}
