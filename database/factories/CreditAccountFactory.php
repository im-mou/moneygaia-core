<?php

namespace Database\Factories;

use App\Models\CreditAccount;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class CreditAccountFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CreditAccount::class;

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
            'balance' => $this->faker->randomFloat(2, -500, 2000),
            'active' => $this->faker->boolean(85),
            "created_at" => Carbon::now()->format('Y-m-d H:i:s'),
            "updated_at" => Carbon::now()->format('Y-m-d H:i:s'),
        ];
    }
}
