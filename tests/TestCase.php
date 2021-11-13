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
        return Carbon::parse($date)->toIso8601ZuluString('microsecond');
    }

    public function randomPrice(int $min = 0, int $max = 500)
    {
        return (float) number_format($this->faker->randomFloat(6, $min, $max), 2, '.', '');
    }

    public function newUser(array $user_attributes = [], array $user_info_attributes = [])
    {
        $user = $this->create('User', $user_attributes, false);
        $this->create('UserInfo', array_merge(["user_id" => $user->id], $user_info_attributes), false);

        return $user;
    }
}
