<?php

namespace Database\Factories;

use App\Models\Goal;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class GoalFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Goal::class;

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
            'ammount' => $this->faker->randomFloat(2, 5, 2000),
            'due_date' => $this->faker->dateTimeBetween('+0 days', '+2 month'),
            'achived' => $this->faker->boolean(50),
            "created_at" => Carbon::now()->format('Y-m-d H:i:s'),
            "updated_at" => Carbon::now()->format('Y-m-d H:i:s'),
        ];
    }
}
