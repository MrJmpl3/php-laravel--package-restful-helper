<?php

use Illuminate\Support\Facades\DB;
use MrJmpl3\LaravelRestfulHelper\Tests\Models\TestModel;

test('check fields', function () {
    TestModel::factory()->count(499)->create();

    TestModel::factory()->create([
        'name' => 'MrJmpl3',
    ]);

    DB::enableQueryLog();

    $response = $this->getJson('/testing-legacy-collection-string?fields=nick,email')->assertOk();

    DB::disableQueryLog();

    $this->assertEquals('select `name` from `test_models` limit 15 offset 0', collect(DB::getQueryLog())->pluck('query')->last());

    $this->assertEquals(1, $response['current_page']);
    $this->assertCount(15, $response['data']);
    $this->assertEquals('http://localhost/testing-legacy-collection-string?fields=nick%2Cemail&page=1', $response['first_page_url']);
    $this->assertEquals(1, $response['from']);
    $this->assertEquals(34, $response['last_page']);
    $this->assertEquals('http://localhost/testing-legacy-collection-string?fields=nick%2Cemail&page=34', $response['last_page_url']);
    $this->assertCount(15, $response['links']);
    $this->assertEquals('http://localhost/testing-legacy-collection-string?fields=nick%2Cemail&page=2', $response['next_page_url']);
    $this->assertEquals('http://localhost/testing-legacy-collection-string', $response['path']);
    $this->assertEquals(15, $response['per_page']);
    $this->assertEquals(null, $response['prev_page_url']);
    $this->assertEquals(15, $response['to']);
    $this->assertEquals(500, $response['total']);
});
