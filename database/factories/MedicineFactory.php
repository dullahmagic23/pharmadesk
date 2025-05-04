<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class MedicineFactory extends Factory
{
    public function definition(): array
    {
        return [
            'id' => Str::uuid(),
            'name' => $this->faker->unique()->word(),
            'brand' => $this->faker->company(),
            'category' => $this->faker->randomElement(['Antibiotic', 'Painkiller', 'Vitamin', 'Antiseptic', 'Antihistamine']),
            'price' => $this->faker->randomFloat(2, 5, 500),
            'description' => $this->faker->sentence(),
        ];
    }
}
