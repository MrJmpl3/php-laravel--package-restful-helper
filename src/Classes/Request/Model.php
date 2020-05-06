<?php
/**
 * Copyright (c) 2020.
 * Archivo desarrollado por Jose Manuel Casani Guerra bajo el pseudonimo de MrJmpl3.
 *
 * Email: jmpl3.soporte@gmail.com
 * Twitter: @MrJmpl3
 * Pagina Web: https://mrjmpl3-official.es
 */
namespace MrJmpl3\LaravelRestfulHelper\Classes\Request;

use Illuminate\Database\Eloquent\Model as EloquentModel;

class Model implements Request
{
    /**
     * @var \Illuminate\Database\Eloquent\Model
     */
    private $model;

    public function __construct(EloquentModel $model)
    {
        $this->model = $model;
    }

    public function getTransformers(): array
    {
        if (property_exists($this->model, 'apiTransforms')) {
            return $this->model->apiTransforms;
        }

        return [];
    }

    public function getExcludeFilter(): array
    {
        if (property_exists($this->model, 'apiExcludeFilter')) {
            return $this->model->apiExcludeFilter;
        }

        return [];
    }

    public function getAcceptRelations(): array
    {
        if (property_exists($this->model, 'apiAcceptRelations')) {
            return $this->model->apiAcceptRelations;
        }

        return [];
    }

    public function getModel(): EloquentModel
    {
        return $this->model;
    }

    public function getBuilder(): \Illuminate\Database\Eloquent\Builder
    {
        return $this->model->newModelQuery();
    }
}
