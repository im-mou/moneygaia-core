<?php

namespace Database\Factories;

use App\Models\CreditAccountType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class CreditAccountTypeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CreditAccountType::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->word(),
            'type' => $this->faker->creditCardType(),
            'enabled' => $this->faker->boolean(95),
            "created_at" => Carbon::now()->format('Y-m-d H:i:s'),
            "updated_at" => Carbon::now()->format('Y-m-d H:i:s'),
        ];
    }
}
