# LARAVEL RESTFUL HELPER

[![Latest Version on Packagist](https://img.shields.io/packagist/v/mrjmpl3/laravel-restful-helper.svg?style=flat-square&include_prereleases)](https://packagist.org/packages/mrjmpl3/laravel-restful-helper)

[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/MrJmpl3/laravel--package-restful-helper/run-tests?label=tests)](https://github.com/MrJmpl3/laravel--package-restful-helper/actions?query=workflow%3Arun-tests+branch%3Amain)

[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/MrJmpl3/laravel--package-restful-helper/Check%20&%20fix%20styling?label=code%20style)](https://github.com/MrJmpl3/laravel--package-restful-helper/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)

[![Total Downloads](https://img.shields.io/packagist/dt/mrjmpl3/laravel-restful-helper.svg?style=flat-square)](https://packagist.org/packages/mrjmpl3/laravel-restful-helper)

This packages make queries depends of the request, like GraphQL.

## Installation

You can install the package via composer:

```bash
composer require mrjmpl3/laravel-restful-helper
```
You can publish the config file with:

```bash
php artisan vendor:publish --provider="MrJmpl3\LaravelRestfulHelper\LaravelRestfulHelperServiceProvider"
```

## Usage

### Configuration

- Register the model in the structures data of configuration like:

```php
'structures' => [
    [
        'model' => \MrJmpl3\LaravelRestfulHelper\Tests\Classes\TestModel::class,
        'data' => [
            [
                'routes' => ['test.index', 'test.show'],
                'transformer' => 'transformersV1',
                'fieldGroupName' => 'fieldGroupNameV1',
                'allowedFields' => 'allowedFieldsV1',
                'allowedFilters' => 'allowedFiltersV1',
                'allowedSorts' => 'allowedSortsV1'
            ],
        ],
    ],
],
```

- The item 'transformer', 'fieldGroupName', 'allowedFields', 'allowedFilters', 'allowedSorts' and 'allowedRelations' are optional

- All items value is a variable in model class, see [Model example](tests/Models/TestModel.php)

- The item 'transformer' is an array where the key is the column of the table and the value is the alias.

- The item 'fieldGroupName' is a string with the alias is the table name of the model when use the field request.

- The item 'allowedFields' is an array with the columns allowed to the field request.

- The item 'allowedFilters' is an array with the columns allowed to the filter request.

- The item 'allowedSorts' is an array with the columns allowed to the sort request.

### Code

- To Collection from model string

```php
$apiHelper = new LaravelRestfulHelper(TestModel::class);
$response = $apiHelper->toCollection();
```

- To Collection from model object

```php
$modelObject = new TestModel();

$apiHelper = new LaravelRestfulHelper($modelObject);
$response = $apiHelper->toCollection();
```

- To Collection from builder query

```php
$builderQuery = TestModel::where('id', '=', 1);

$apiHelper = new LaravelRestfulHelper($builderQuery);
$response = $apiHelper->toCollection();
```

### Requests

- **Fields or select data**: `/users?fields[users]=name,email`
- **Filter data**: `/users?filter[name]=john&filter[email]=gmail`
- **Sort data**: `/users?sort=-name,email`
  - With the negative prefix = desc
  - Without the negative prefix = asc
- **Paginate data**: `/users?paginate=true&page[size]=5&page[number]=1`

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Jose Manuel Casani Guerra](https://github.com/mrjmpl3)

- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
