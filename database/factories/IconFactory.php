<?php

namespace Database\Factories;

use App\Models\Icon;
use Illuminate\Database\Eloquent\Factories\Factory;

class IconFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Icon::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->word(),
            'value' => $this->faker->word(),
            'enabled' => $this->faker->boolean(80),
            'group' => $this->faker->randomElement(Icon::ALLOWED_GROUPS)
        ];
    }
}
