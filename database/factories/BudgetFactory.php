<?php

namespace Database\Factories;

use App\Models\Budget;
use Illuminate\Database\Eloquent\Factories\Factory;

class BudgetFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Budget::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->word(),
            'description' => $this->faker->sentence(10),
            'ammount' => $this->faker->randomFloat(2, 0, 500),
            'start_date' => $this->faker->dateTimeBetween('+0 days', '+1 month'),
            'end_date' => $this->faker->dateTimeBetween('+1 month', '+2 month'),
        ];
    }
}
