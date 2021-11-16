<?php
namespace MrJmpl3\LaravelRestfulHelper\Tests\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use MrJmpl3\LaravelRestfulHelper\Tests\Factories\TestModelFactory;

class TestModel extends Model
{
    use HasFactory;

    public $transformersV1 = [
        'name' => 'nick',
    ];

    public $fieldGroupNameV1 = 'user';

    public $allowedFieldsV1 = [
        'name',
        'is_active',
    ];

    public $allowedFiltersV1 = [
        'name',
        'is_active',
    ];

    public $allowedSortsV1 = [
        'name',
        'is_active',
    ];

    protected $guarded = [];

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return TestModelFactory::new();
    }
}
