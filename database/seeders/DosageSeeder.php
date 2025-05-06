<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class DosageSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('dosages')->insert([
            [
                'id' => Str::uuid(),
                'frequency' => '1 tablet twice daily',
                'instructions' => 'Take one tablet in the morning and one in the evening.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'frequency' => '5ml syrup three times a day',
                'instructions' => 'Administer 5ml after meals.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'frequency' => 'Apply ointment once daily',
                'instructions' => 'Apply a thin layer on the affected area before bedtime.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'frequency' => '1 capsule every 8 hours',
                'instructions' => 'Take one capsule every 8 hours with water.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}

