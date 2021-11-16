<?php

use MrJmpl3\LaravelRestfulHelper\Tests\Models\TestModel;

test('check structures is returned with the correct route', function () {
    TestModel::factory()->count(5)->create();

    $response = $this->getJson('/testing-model-structure')->assertOk();

    $this->assertEquals([
        'routes' => ['testing-model-structure', 'testing-legacy-collection-string', 'testing-collection-string'],
        'transformer' => 'transformersV1',
        'fieldGroupName' => 'fieldGroupNameV1',
        'allowedRelations' => 'allowedRelationsV1',
        'allowedFilters' => 'allowedFiltersV1',
        'allowedSorts' => 'allowedSortsV1',
    ], $response['structure']);
});

test('check structures is returned with another route', function () {
    TestModel::factory()->count(5)->create();

    $response = $this->getJson('/testing-model-fail-structure')->assertOk();

    $this->assertEquals([], $response['structure']);
});
