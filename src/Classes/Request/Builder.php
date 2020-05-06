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

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

class Builder implements Request
{
    /**
     * @var \Illuminate\Database\Eloquent\Builder
     */
    private $builder;

    public function __construct(EloquentBuilder $builder)
    {
        $this->builder = $builder;
    }

    public function getTransformers(): array
    {
        if (property_exists($this->builder->getModel(), 'apiTransforms')) {
            return $this->builder->getModel()->apiTransforms;
        }

        return [];
    }

    public function getExcludeFilter(): array
    {
        if (property_exists($this->builder->getModel(), 'apiExcludeFilter')) {
            return $this->builder->getModel()->apiExcludeFilter;
        }

        return [];
    }

    public function getAcceptRelations(): array
    {
        if (property_exists($this->builder->getModel(), 'apiAcceptRelations')) {
            return $this->builder->getModel()->apiAcceptRelations;
        }

        return [];
    }

    public function getModel(): \Illuminate\Database\Eloquent\Model
    {
        return $this->builder->getModel();
    }

    public function getBuilder(): EloquentBuilder
    {
        return $this->builder;
    }
}
