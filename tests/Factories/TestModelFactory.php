<?php
namespace MrJmpl3\LaravelRestfulHelper\Tests\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use MrJmpl3\LaravelRestfulHelper\Tests\Models\TestModel;

class TestModelFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var null|string
     */
    protected $model = TestModel::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'is_active' => $this->faker->boolean(),
        ];
    }
}
