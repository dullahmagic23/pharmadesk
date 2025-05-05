<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Over-the-Counter Medications
        Product::create([
            'name' => 'Cold and Allergy Relief',
            'category' => 'Over-the-Counter Medications',
            'description' => 'Medications for coughs, colds, allergies, and sinus congestion.',
            'unit' => 'Box',
        ]);
        Product::create([
            'name' => 'Pain Relief (Ibuprofen)',
            'category' => 'Over-the-Counter Medications',
            'description' => 'Pain relievers like ibuprofen and acetaminophen.',
            'unit' => 'Bottle',
        ]);
        Product::create([
            'name' => 'Digestive Health (Constipation Relief)',
            'category' => 'Over-the-Counter Medications',
            'description' => 'Products for constipation, diarrhea, and heartburn.',
            'unit' => 'Pack',
        ]);
        Product::create([
            'name' => 'Wound Care Kit',
            'category' => 'Over-the-Counter Medications',
            'description' => 'Bandages, antiseptic wipes, and other first-aid items.',
            'unit' => 'Kit',
        ]);

        // Personal Care Items
        Product::create([
            'name' => 'Skincare Lotion',
            'category' => 'Personal Care Items',
            'description' => 'Lotions, creams, sunscreens, and acne treatments.',
            'unit' => 'Bottle',
        ]);
        Product::create([
            'name' => 'Shampoo & Conditioner',
            'category' => 'Personal Care Items',
            'description' => 'Shampoos, conditioners, and styling products.',
            'unit' => 'Bottle',
        ]);
        Product::create([
            'name' => 'Toothbrush & Toothpaste',
            'category' => 'Personal Care Items',
            'description' => 'Oral hygiene products including toothbrushes and toothpaste.',
            'unit' => 'Set',
        ]);
        Product::create([
            'name' => 'Makeup (Foundation)',
            'category' => 'Personal Care Items',
            'description' => 'Foundation and other makeup products.',
            'unit' => 'Box',
        ]);

        // Health-Related Products
        Product::create([
            'name' => 'Multivitamins',
            'category' => 'Health-Related Products',
            'description' => 'A range of vitamins, minerals, and herbal supplements.',
            'unit' => 'Bottle',
        ]);
        Product::create([
            'name' => 'Blood Pressure Monitor',
            'category' => 'Health-Related Products',
            'description' => 'Devices for monitoring blood pressure.',
            'unit' => 'Piece',
        ]);
        Product::create([
            'name' => 'Oxygen Concentrator',
            'category' => 'Health-Related Products',
            'description' => 'Medical device for oxygen therapy.',
            'unit' => 'Piece',
        ]);
        Product::create([
            'name' => 'Wheelchair',
            'category' => 'Health-Related Products',
            'description' => 'Wheelchair for mobility assistance.',
            'unit' => 'Piece',
        ]);

        // Other Potential Products
        Product::create([
            'name' => 'Turmeric Spice',
            'category' => 'Other Potential Products',
            'description' => 'A spice used in traditional medicine.',
            'unit' => 'Jar',
        ]);
        Product::create([
            'name' => 'Gluten-Free Snacks',
            'category' => 'Other Potential Products',
            'description' => 'Dietetic foods such as gluten-free or low-sugar snacks.',
            'unit' => 'Pack',
        ]);
        Product::create([
            'name' => 'Flu Shot',
            'category' => 'Other Potential Products',
            'description' => 'Seasonal flu vaccine available during specific times of the year.',
            'unit' => 'Injection',
        ]);
    }
}

