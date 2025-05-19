<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MedicineUnitSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('medicine_units')->insert([
            [
                'id' => Str::uuid(),
                'unit_name' => 'Tablet',
                'abbreviation' => 'tab',
                'description' => 'Single solid dose unit',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'unit_name' => 'Capsule',
                'abbreviation' => 'cap',
                'description' => 'Solid dose in capsule form',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'unit_name' => 'Syrup',
                'abbreviation' => 'ml',
                'description' => 'Liquid medication in milliliters',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'unit_name' => 'Injection',
                'abbreviation' => 'inj',
                'description' => 'Single ampoule or vial',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'unit_name' => 'Tube',
                'abbreviation' => 'tube',
                'description' => 'Ointments or creams',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'unit_name' => 'Pack',
                'abbreviation' => 'pack',
                'description' => 'Group of tablets or capsules',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'unit_name' => 'Box',
                'abbreviation' => 'box',
                'description' => 'Contains packs or strips',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'unit_name' => 'Strip',
                'abbreviation' => 'strip',
                'description' => 'A strip of tablets or capsules',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'unit_name' => 'Bottle',
                'abbreviation' => 'bottle',
                'description' => 'Used for syrups or drops',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'unit_name' => 'Drop',
                'abbreviation' => 'ml',
                'description' => 'Used for eye/ear/nose drops',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [   
                'id'=> Str::uuid(),
                'unit_name' => 'Canister',
                'abbreviation' => 'can',
                'description' => 'Inhalers or aerosol medicines',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id'=> Str::uuid(),
                'unit_name' => 'Unit',
                'abbreviation' => 'unit',
                'description' => 'Generic inventory unit',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
