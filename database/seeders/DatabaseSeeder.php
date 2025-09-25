<?php

namespace Database\Seeders;

use App\Models\Doctor;
use App\Models\User;
use App\Models\Customer;
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


        $this->call([
            RoleSeeder::class,
            DosageSeeder::class,
            // UserSeeder::class,
            MedicineUnitSeeder::class,
            MedicineCategorySeeder::class,
            ProductSeeder::class,
            MedicineSeeder::class,
            // Add other seeders here
        ]);

        $user = User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@pharmadesk.test',
            'password' => bcrypt('password'),
        ]);

        $user->assignRole('admin');

        Customer::create([
            'name' => 'Walk-in Customer',
        ]);
    }
}
