<?php

use Illuminate\Support\Facades\DB;
use MrJmpl3\LaravelRestfulHelper\Tests\Models\TestModel;

test('check fields', function () {
    $this->withoutExceptionHandling();

    TestModel::factory()->count(499)->create();

    TestModel::factory()->create([
        'name' => 'MrJmpl3',
    ]);

    DB::enableQueryLog();

    $response = $this->getJson('/testing-collection-string?fields[user]=name,is_active')->assertOk();

    DB::disableQueryLog();

    $this->assertEquals('select `test_models`.`name`, `test_models`.`is_active` from `test_models` limit 30 offset 0', collect(DB::getQueryLog())->pluck('query')->last());

    $this->assertEquals(1, $response['current_page']);
    $this->assertCount(30, $response['data']);
    $this->assertEquals('http://localhost/testing-collection-string?fields%5Buser%5D=name%2Cis_active&page%5Bnumber%5D=1', $response['first_page_url']);
    $this->assertEquals(1, $response['from']);
    $this->assertEquals(17, $response['last_page']);
    $this->assertEquals('http://localhost/testing-collection-string?fields%5Buser%5D=name%2Cis_active&page%5Bnumber%5D=17', $response['last_page_url']);
    $this->assertCount(15, $response['links']);
    $this->assertEquals('http://localhost/testing-collection-string?fields%5Buser%5D=name%2Cis_active&page%5Bnumber%5D=2', $response['next_page_url']);
    $this->assertEquals('http://localhost/testing-collection-string', $response['path']);
    $this->assertEquals(30, $response['per_page']);
    $this->assertEquals(null, $response['prev_page_url']);
    $this->assertEquals(30, $response['to']);
    $this->assertEquals(500, $response['total']);
});

test('check full fields', function () {
    $this->withoutExceptionHandling();

    TestModel::factory()->count(499)->create();

    TestModel::factory()->create([
        'name' => 'MrJmpl3',
    ]);

    DB::enableQueryLog();

    $response = $this->getJson('/testing-collection-string')->assertOk();

    DB::disableQueryLog();

    $this->assertEquals('select * from `test_models` limit 30 offset 0', collect(DB::getQueryLog())->pluck('query')->last());

    $this->assertEquals(1, $response['current_page']);
    $this->assertCount(30, $response['data']);
    $this->assertEquals('http://localhost/testing-collection-string?page%5Bnumber%5D=1', $response['first_page_url']);
    $this->assertEquals(1, $response['from']);
    $this->assertEquals(17, $response['last_page']);
    $this->assertEquals('http://localhost/testing-collection-string?page%5Bnumber%5D=17', $response['last_page_url']);
    $this->assertCount(15, $response['links']);
    $this->assertEquals('http://localhost/testing-collection-string?page%5Bnumber%5D=2', $response['next_page_url']);
    $this->assertEquals('http://localhost/testing-collection-string', $response['path']);
    $this->assertEquals(30, $response['per_page']);
    $this->assertEquals(null, $response['prev_page_url']);
    $this->assertEquals(30, $response['to']);
    $this->assertEquals(500, $response['total']);
});
