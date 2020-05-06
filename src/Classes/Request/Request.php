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

interface Request
{
    public function getTransformers(): array;

    public function getExcludeFilter(): array;

    public function getAcceptRelations(): array;

    public function getModel(): \Illuminate\Database\Eloquent\Model;

    public function getBuilder(): \Illuminate\Database\Eloquent\Builder;
}
