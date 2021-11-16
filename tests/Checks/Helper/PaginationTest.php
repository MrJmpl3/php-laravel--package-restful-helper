<?php

use Illuminate\Support\Facades\DB;
use MrJmpl3\LaravelRestfulHelper\Tests\Models\TestModel;

test('check pagination', function () {
    TestModel::factory()->count(500)->create();

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

test('check pagination with custom page', function () {
    TestModel::factory()->count(500)->create();

    DB::enableQueryLog();

    $response = $this->getJson('/testing-collection-string?page[number]=5')->assertOk();

    DB::disableQueryLog();

    $this->assertEquals('select * from `test_models` limit 30 offset 120', collect(DB::getQueryLog())->pluck('query')->last());

    $this->assertEquals(5, $response['current_page']);
    $this->assertCount(30, $response['data']);
    $this->assertEquals('http://localhost/testing-collection-string?page%5Bnumber%5D=1', $response['first_page_url']);
    $this->assertEquals(121, $response['from']);
    $this->assertEquals(17, $response['last_page']);
    $this->assertEquals('http://localhost/testing-collection-string?page%5Bnumber%5D=17', $response['last_page_url']);
    $this->assertCount(15, $response['links']);
    $this->assertEquals('http://localhost/testing-collection-string?page%5Bnumber%5D=6', $response['next_page_url']);
    $this->assertEquals('http://localhost/testing-collection-string', $response['path']);
    $this->assertEquals(30, $response['per_page']);
    $this->assertEquals('http://localhost/testing-collection-string?page%5Bnumber%5D=4', $response['prev_page_url']);
    $this->assertEquals(150, $response['to']);
    $this->assertEquals(500, $response['total']);
});

test('check pagination with custom page size', function () {
    TestModel::factory()->count(500)->create();

    DB::enableQueryLog();

    $response = $this->getJson('/testing-collection-string?page[size]=10')->assertOk();

    DB::disableQueryLog();

    $this->assertEquals('select * from `test_models` limit 10 offset 0', collect(DB::getQueryLog())->pluck('query')->last());

    $this->assertEquals(1, $response['current_page']);
    $this->assertCount(10, $response['data']);
    $this->assertEquals('http://localhost/testing-collection-string?page%5Bsize%5D=10&page%5Bnumber%5D=1', $response['first_page_url']);
    $this->assertEquals(1, $response['from']);
    $this->assertEquals(50, $response['last_page']);
    $this->assertEquals('http://localhost/testing-collection-string?page%5Bsize%5D=10&page%5Bnumber%5D=50', $response['last_page_url']);
    $this->assertCount(15, $response['links']);
    $this->assertEquals('http://localhost/testing-collection-string?page%5Bsize%5D=10&page%5Bnumber%5D=2', $response['next_page_url']);
    $this->assertEquals('http://localhost/testing-collection-string', $response['path']);
    $this->assertEquals(10, $response['per_page']);
    $this->assertEquals(null, $response['prev_page_url']);
    $this->assertEquals(10, $response['to']);
    $this->assertEquals(500, $response['total']);
});

test('check pagination with custom page and custom page size', function () {
    TestModel::factory()->count(500)->create();

    DB::enableQueryLog();

    $response = $this->getJson('/testing-collection-string?page[number]=5&page[size]=10')->assertOk();

    DB::disableQueryLog();

    $this->assertEquals('select * from `test_models` limit 10 offset 40', collect(DB::getQueryLog())->pluck('query')->last());

    $this->assertEquals(5, $response['current_page']);
    $this->assertCount(10, $response['data']);
    $this->assertEquals('http://localhost/testing-collection-string?page%5Bsize%5D=10&page%5Bnumber%5D=1', $response['first_page_url']);
    $this->assertEquals(41, $response['from']);
    $this->assertEquals(50, $response['last_page']);
    $this->assertEquals('http://localhost/testing-collection-string?page%5Bsize%5D=10&page%5Bnumber%5D=50', $response['last_page_url']);
    $this->assertCount(15, $response['links']);
    $this->assertEquals('http://localhost/testing-collection-string?page%5Bsize%5D=10&page%5Bnumber%5D=6', $response['next_page_url']);
    $this->assertEquals('http://localhost/testing-collection-string', $response['path']);
    $this->assertEquals(10, $response['per_page']);
    $this->assertEquals('http://localhost/testing-collection-string?page%5Bsize%5D=10&page%5Bnumber%5D=4', $response['prev_page_url']);
    $this->assertEquals(50, $response['to']);
    $this->assertEquals(500, $response['total']);
});
