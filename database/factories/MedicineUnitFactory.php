<?php

namespace Database\Factories;

use App\Models\Medicine;
use App\Models\MedicineUnit;
use Illuminate\Database\Eloquent\Factories\Factory;

class MedicineUnitFactory extends Factory
{
    protected $model = MedicineUnit::class;

    public function definition(): array
    {
        $unitNames = ['Tablet', 'Dose', 'Capsule', 'Bottle', 'Syrup'];

        return [
            'medicine_id' => Medicine::inRandomOrder()->first()?->id ?? Medicine::factory(),
            'unit_name' => $unit = $this->faker->randomElement($unitNames),
            'retail_price' => $retail = $this->faker->randomFloat(2, 1, 50),
            'wholesale_price' => $retail * 0.8,
            'wholesale_min_quantity' => $this->faker->numberBetween(10, 100),
        ];
    }
}
