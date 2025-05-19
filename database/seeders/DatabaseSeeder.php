<?php

namespace Database\Seeders;

use App\Models\Doctor;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
//        Doctor::factory(10)->create();
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        $this->call([
            // DosageSeeder::class
//             UserSeeder::class
             MedicineUnitSeeder::class,
             MedicineCategorySeeder::class,
            ProductSeeder::class,
            MedicineSeeder::class,
            // Add other seeders here
        ]);
    }
}
