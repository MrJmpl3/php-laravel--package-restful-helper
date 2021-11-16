<?php

use Illuminate\Support\Facades\DB;
use MrJmpl3\LaravelRestfulHelper\Tests\Models\TestModel;

test('check filter', function () {
    $this->withoutExceptionHandling();

    TestModel::factory()->count(499)->create();

    TestModel::factory()->create([
        'name' => 'MrJmpl3',
    ]);

    DB::enableQueryLog();

    $response = $this->getJson('/testing-collection-string?filter[nick]=mrjmpl3')->assertOk();

    DB::disableQueryLog();

    $this->assertEquals('select * from `test_models` where `test_models`.`name` = ? limit 30 offset 0', collect(DB::getQueryLog())->pluck('query')->last());
    $this->assertEquals(['mrjmpl3'], collect(DB::getQueryLog())->pluck('bindings')->last());

    $this->assertEquals(1, $response['current_page']);
    $this->assertCount(1, $response['data']);
    $this->assertEquals('http://localhost/testing-collection-string?filter%5Bnick%5D=mrjmpl3&page%5Bnumber%5D=1', $response['first_page_url']);
    $this->assertEquals(1, $response['from']);
    $this->assertEquals(1, $response['last_page']);
    $this->assertEquals('http://localhost/testing-collection-string?filter%5Bnick%5D=mrjmpl3&page%5Bnumber%5D=1', $response['last_page_url']);
    $this->assertCount(3, $response['links']);
    $this->assertEquals(null, $response['next_page_url']);
    $this->assertEquals('http://localhost/testing-collection-string', $response['path']);
    $this->assertEquals(30, $response['per_page']);
    $this->assertEquals(null, $response['prev_page_url']);
    $this->assertEquals(1, $response['to']);
    $this->assertEquals(1, $response['total']);
});
