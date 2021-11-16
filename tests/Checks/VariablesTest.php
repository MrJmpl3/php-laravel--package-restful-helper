<?php

use MrJmpl3\LaravelRestfulHelper\LaravelRestfulHelper;
use MrJmpl3\LaravelRestfulHelper\Tests\Models\TestModel;

test('test variables from model string', function () {
    $apiHelper = new LaravelRestfulHelper(TestModel::class);

    $this->assertEquals(new TestModel(), $apiHelper->model);
    $this->assertEquals(TestModel::query(), $apiHelper->queryBuilder);
});

test('test variables from model object', function () {
    $modelObject = new TestModel();

    $apiHelper = new LaravelRestfulHelper($modelObject);

    $this->assertEquals($modelObject, $apiHelper->model);
    $this->assertEquals($modelObject::query(), $apiHelper->queryBuilder);
});

test('test variables from builder query', function () {
    $builderQuery = TestModel::where('id', '=', 1);

    $apiHelper = new LaravelRestfulHelper($builderQuery);

    $this->assertEquals($builderQuery->getModel(), $apiHelper->model);
    $this->assertEquals($builderQuery, $apiHelper->queryBuilder);
});
