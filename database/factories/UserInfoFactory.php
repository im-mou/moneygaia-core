<?php

namespace Database\Factories;

use App\Models\UserInfo;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Config;

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
            'date_format' => Config::get('constants.user_info_default_date_format'),
            "created_at" => Carbon::now()->format('Y-m-d H:i:s'),
            "updated_at" => Carbon::now()->format('Y-m-d H:i:s'),
        ];
    }
}
