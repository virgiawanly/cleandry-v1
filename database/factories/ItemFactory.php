<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->word(),
            'qty' => $this->faker->numberBetween(1, 200),
            'price' => $this->faker->numberBetween(10000, 3000000),
            'buy_date' => now(),
            'supplier' => $this->faker->company(),
            'status' => $this->faker->randomElement(['submission', 'out_of_stock', 'available'])
        ];
    }
}
