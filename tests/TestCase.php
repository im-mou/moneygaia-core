<?php

namespace Tests;

use DateTime;
use Faker\Factory;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    private $faker = null;

    public function __construct()
    {
        parent::__construct();
        $this->faker = Factory::create();
    }

    public function create(string $model, array $attributes = [], bool $resource = true)
    {
        $resourceModel = ("App\\Models\\$model")::factory()->create($attributes);

        if (!$resource) {
            return $resourceModel;
        }

        $resourceClass = "App\\Http\\Resources\\$model" . "Resource";

        return new $resourceClass($resourceModel);
    }

    public function mockDate(DateTime $date)
    {
        return Carbon::parse($date)->toIso8601ZuluString("microsecond");
    }

    /**
     * Provides a natural looking price between $min and $max.
     * It will also round it to the nearest tenth and substract 1 to give it a psychological impact.
     * Then it will add pricing typical decimals (X.29 or X.99)
     *
     * @param integer $min
     * @param integer $max
     * @param boolean $tenths
     * @param boolean $psychologicalPrice
     * @param boolean $decimals
     * @return float|int
     */
    public function randomPrice($min = 10, $max = 500, $tenths = true, $psychologicalPrice = true, $decimals = true)
    {
        if ($decimals) {
            $price = $this->faker->randomFloat(2, $min, $max);
        } else {
            $price = $this->faker->numberBetween($min, $max);
        }

        if ($tenths) {
            $price = round($price, -1);
            if ($psychologicalPrice) {
                $price = $price - 1;
            }
        }

        if ($decimals) {
            $price += $this->faker->randomElement([0.29, 0.49, 0.99]);
        }

        return $price;
    }

    public function newUser(array $user_attributes = [], array $user_info_attributes = [])
    {
        $user = $this->create("User", $user_attributes, false);
        $this->create("UserInfo", array_merge(["user_id" => $user->id], $user_info_attributes), false);

        return $user;
    }
}
