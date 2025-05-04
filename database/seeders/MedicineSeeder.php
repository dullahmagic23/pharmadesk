<?php

namespace Database\Seeders;

use App\Models\Medicine;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MedicineSeeder extends Seeder
{
    public function run(): void
    {
        $medicines = [
            [
                'name' => 'Paracetamol',
                'brand' => 'Panadol',
                'category' => 'Painkiller',
                'price' => 15.00,
                'description' => 'Used to treat mild to moderate pain and reduce fever.',
            ],
            [
                'name' => 'Amoxicillin',
                'brand' => 'Amoxil',
                'category' => 'Antibiotic',
                'price' => 120.00,
                'description' => 'Common antibiotic used for bacterial infections.',
            ],
            [
                'name' => 'Cetirizine',
                'brand' => 'Zyrtec',
                'category' => 'Antihistamine',
                'price' => 25.50,
                'description' => 'Used to relieve allergy symptoms like runny nose and sneezing.',
            ],
            [
                'name' => 'Ibuprofen',
                'brand' => 'Brufen',
                'category' => 'Painkiller',
                'price' => 35.75,
                'description' => 'Anti-inflammatory used for pain, swelling, and fever.',
            ],
            [
                'name' => 'Metformin',
                'brand' => 'Glucophage',
                'category' => 'Antidiabetic',
                'price' => 60.00,
                'description' => 'First-line medication for type 2 diabetes.',
            ],
            [
                'name' => 'Loratadine',
                'brand' => 'Claritin',
                'category' => 'Antihistamine',
                'price' => 30.00,
                'description' => 'Non-drowsy antihistamine for allergies and hay fever.',
            ],
            [
                'name' => 'Azithromycin',
                'brand' => 'Zithromax',
                'category' => 'Antibiotic',
                'price' => 95.00,
                'description' => 'Used to treat various bacterial infections.',
            ],
            [
                'name' => 'Diclofenac',
                'brand' => 'Voltaren',
                'category' => 'Anti-inflammatory',
                'price' => 45.00,
                'description' => 'Used to reduce inflammation and relieve pain.',
            ],
            [
                'name' => 'Omeprazole',
                'brand' => 'Losec',
                'category' => 'Antacid',
                'price' => 40.00,
                'description' => 'Treats acid reflux, ulcers, and stomach acid issues.',
            ],
            [
                'name' => 'Chlorpheniramine',
                'brand' => 'Piriton',
                'category' => 'Antihistamine',
                'price' => 18.00,
                'description' => 'Used for allergies, cold symptoms, and itching.',
            ],
            [
                'name' => 'Salbutamol',
                'brand' => 'Ventolin',
                'category' => 'Bronchodilator',
                'price' => 80.00,
                'description' => 'Relieves symptoms of asthma and chronic bronchitis.',
            ],
            [
                'name' => 'Folic Acid',
                'brand' => 'Folvite',
                'category' => 'Vitamin',
                'price' => 20.00,
                'description' => 'Used for folate deficiency and prenatal care.',
            ],
            [
                'name' => 'Hydrocortisone',
                'brand' => 'Cortef',
                'category' => 'Steroid',
                'price' => 55.00,
                'description' => 'Reduces inflammation, itching, and allergic reactions.',
            ],
            [
                'name' => 'Ranitidine',
                'brand' => 'Zantac',
                'category' => 'Antacid',
                'price' => 22.00,
                'description' => 'Used to reduce stomach acid and treat ulcers.',
            ],
            [
                'name' => 'Ciprofloxacin',
                'brand' => 'Cipro',
                'category' => 'Antibiotic',
                'price' => 90.00,
                'description' => 'Treats infections like UTIs and respiratory issues.',
            ],
            [
                'name' => 'Aspirin',
                'brand' => 'Disprin',
                'category' => 'Painkiller',
                'price' => 12.00,
                'description' => 'Used to relieve pain, reduce fever, and prevent blood clots.',
            ],
            [
                'name' => 'Vitamin C',
                'brand' => 'Redoxon',
                'category' => 'Vitamin',
                'price' => 35.00,
                'description' => 'Boosts immunity and treats vitamin C deficiency.',
            ],
            [
                'name' => 'Loperamide',
                'brand' => 'Imodium',
                'category' => 'Antidiarrheal',
                'price' => 25.00,
                'description' => 'Used to reduce the frequency of diarrhea.',
            ],
            [
                'name' => 'Multivitamins',
                'brand' => 'Wellman',
                'category' => 'Vitamin',
                'price' => 50.00,
                'description' => 'Daily supplement for general health and nutrition.',
            ],
            [
                'name' => 'Betadine',
                'brand' => 'Betadine',
                'category' => 'Antiseptic',
                'price' => 28.00,
                'description' => 'Used for wound cleaning and skin disinfection.',
            ],
        ];

        foreach ($medicines as $data) {
            Medicine::create([
                'id' => Str::uuid(),
                ...$data
            ]);
        }
    }
}
