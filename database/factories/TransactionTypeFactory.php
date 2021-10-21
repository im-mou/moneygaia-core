<?php

namespace Database\Factories;

use App\Models\TransactionType;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransactionTypeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TransactionType::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->word(),
            'color' => $this->faker->hexcolor(),
            'outflow' => $this->faker->boolean(95),
        ];
    }
}