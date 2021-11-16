<?php
namespace MrJmpl3\LaravelRestfulHelper\Tests;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Route;
use MrJmpl3\LaravelRestfulHelper\LaravelRestfulHelper;
use MrJmpl3\LaravelRestfulHelper\LaravelRestfulHelperLegacy;
use MrJmpl3\LaravelRestfulHelper\LaravelRestfulHelperServiceProvider;
use MrJmpl3\LaravelRestfulHelper\Tests\Models\TestModel;
use Spatie\JsonApiPaginate\JsonApiPaginateServiceProvider;
use Spatie\QueryBuilder\QueryBuilderServiceProvider;

/**
 * @internal
 *
 * @small
 * @coversNothing
 */
class TestCase extends \Orchestra\Testbench\TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();

        config(['app.debug' => true]);
        config(['restful_helper.structures' => [
            [
                'model' => \MrJmpl3\LaravelRestfulHelper\Tests\Models\TestModel::class,
                'data' => [
                    [
                        'routes' => ['testing-model-structure', 'testing-legacy-collection-string', 'testing-collection-string'],
                        'transformer' => 'transformersV1',
                        'fieldGroupName' => 'fieldGroupNameV1',
                        'allowedFilters' => 'allowedFiltersV1',
                        'allowedSorts' => 'allowedSortsV1',
                        'allowedRelations' => 'allowedRelationsV1',
                    ],
                ],
            ],
        ]]);

        $this->setUpDatabase($this->app);
        $this->setUpFakeRoutes();
    }

    protected function setUpDatabase(Application $app)
    {
        $app['db']->connection()->getSchemaBuilder()->create('test_models', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('name');
            $table->boolean('is_active')->default(true);
        });
    }

    protected function setUpFakeRoutes()
    {
        Route::get('/testing-legacy-collection-string', function () {
            $apiHelper = new LaravelRestfulHelperLegacy(TestModel::class);

            return $apiHelper->toCollection();
        })->name('testing-legacy-collection-string');

        Route::get('/testing-collection-string', function () {
            $apiHelper = new LaravelRestfulHelper(TestModel::class);

            return $apiHelper->toCollection();
        })->name('testing-collection-string');

        Route::get('/testing-model-structure', function () {
            // For structure, is not important is that is legacy or not, because Legacy and Actual class extends from Helper class
            // and RestfulHelper class calculate the structure
            $apiHelper = new LaravelRestfulHelper(TestModel::class);

            return ['model' => $apiHelper->toModel(), 'structure' => $apiHelper->structureFiltered];
        })->name('testing-model-structure');

        Route::get('/testing-model-fail-structure', function () {
            // For structure, is not important is that is legacy or not, because Legacy and Actual class extends from Helper class
            // and RestfulHelper class calculate the structure
            $apiHelper = new LaravelRestfulHelper(TestModel::class);

            return ['model' => $apiHelper->toModel(), 'structure' => $apiHelper->structureFiltered];
        })->name('testing-model-fail-structure');
    }

    protected function getPackageProviders($app): array
    {
        return [QueryBuilderServiceProvider::class, JsonApiPaginateServiceProvider::class, LaravelRestfulHelperServiceProvider::class];
    }
}
