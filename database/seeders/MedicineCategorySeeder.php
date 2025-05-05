<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\MedicineCategory;

class MedicineCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $categories = [
            ['name' => 'Analgesics', 'description' => 'Relieve pain (e.g., non-narcotic and narcotic analgesics).'],
            ['name' => 'Antibacterials/Antibiotics', 'description' => 'Treat bacterial infections.'],
            ['name' => 'Antidepressants', 'description' => 'Treat depression and other mood disorders.'],
            ['name' => 'Anticonvulsants', 'description' => 'Prevent or control seizures.'],
            ['name' => 'Anticoagulants', 'description' => 'Prevent blood clots.'],
            ['name' => 'Antipsychotics', 'description' => 'Treat psychosis and other mental health conditions.'],
            ['name' => 'Antivirals', 'description' => 'Treat viral infections.'],
            ['name' => 'Cardiovascular', 'description' => 'Affect the heart and blood vessels.'],
            ['name' => 'Hormonal Agents', 'description' => 'Affect hormone levels.'],
            ['name' => 'Immunosuppressants', 'description' => 'Suppress the immune system.'],
            ['name' => 'Diuretics', 'description' => 'Help the body get rid of excess fluid.'],
            ['name' => 'Antidiabetics', 'description' => 'Manage blood sugar levels in people with diabetes.'],
            ['name' => 'Antitussives', 'description' => 'Suppress coughing.'],
        ];

        foreach ($categories as $category) {
            MedicineCategory::create(['name' => $category['name'],'description'=>$category['description']]);
        }
    }
}
