<?php

namespace Database\Factories;

use App\Models\UserInfo;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserInfoFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = UserInfo::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'total_balance' => $this->faker->randomFloat(2, -100, 2000),
            'currency' => $this->faker->currencyCode(),
            'country' => $this->faker->countryCode(),
            'language' => $this->faker->languageCode(),
        ];
    }
}
