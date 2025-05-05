<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Medicine;
use App\Models\MedicineUnit;

class MedicineUnitSeeder extends Seeder
{
    public function run()
    {
        $units = ['Tablet', 'Dose', 'Syringe', 'Vial'];

        foreach ($units as $unit) {
            MedicineUnit::create(['unit_name' => $unit]);
        }
    }
}
