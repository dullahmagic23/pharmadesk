<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MedicineSeeder extends Seeder
{
    public function run(): void
    {
        $brands = [
            'Pfizer', 'Novartis', 'GSK', 'Sanofi', 'Johnson & Johnson',
            'AstraZeneca', 'Bayer', 'Merck', 'AbbVie', 'Roche',
            'Sun Pharma', 'Cipla', 'Dr. Reddy’s', 'Lupin', 'Teva',
        ];

        // Fetch category UUIDs from the DB
        $categoryIds = DB::table('medicine_categories')->pluck('id')->toArray();

        if (empty($categoryIds)) {
            $this->command->warn('No categories found. Please seed medicine_categories first.');
            return;
        }

        $medicines = [
            ['name' => 'Paracetamol'],
            ['name' => 'Ibuprofen'],
            ['name' => 'Amoxicillin'],
            ['name' => 'Azithromycin'],
            ['name' => 'Ciprofloxacin'],
            ['name' => 'Metformin'],
            ['name' => 'Atorvastatin'],
            ['name' => 'Omeprazole'],
            ['name' => 'Pantoprazole'],
            ['name' => 'Ranitidine'],
            ['name' => 'Cetirizine'],
            ['name' => 'Loratadine'],
            ['name' => 'Levocetirizine'],
            ['name' => 'Amlodipine'],
            ['name' => 'Losartan'],
            ['name' => 'Enalapril'],
            ['name' => 'Metoprolol'],
            ['name' => 'Propranolol'],
            ['name' => 'Hydrochlorothiazide'],
            ['name' => 'Furosemide'],
            ['name' => 'Spironolactone'],
            ['name' => 'Clopidogrel'],
            ['name' => 'Warfarin'],
            ['name' => 'Insulin'],
            ['name' => 'Glipizide'],
            ['name' => 'Gliclazide'],
            ['name' => 'Pioglitazone'],
            ['name' => 'Sitagliptin'],
            ['name' => 'Linagliptin'],
            ['name' => 'Salbutamol'],
            ['name' => 'Ipratropium'],
            ['name' => 'Fluticasone'],
            ['name' => 'Budesonide'],
            ['name' => 'Montelukast'],
            ['name' => 'Theophylline'],
            ['name' => 'Prednisolone'],
            ['name' => 'Dexamethasone'],
            ['name' => 'Hydrocortisone'],
            ['name' => 'Betamethasone'],
            ['name' => 'Clotrimazole'],
            ['name' => 'Miconazole'],
            ['name' => 'Nystatin'],
            ['name' => 'Ketoconazole'],
            ['name' => 'Terbinafine'],
            ['name' => 'Fluconazole'],
            ['name' => 'Aciclovir'],
            ['name' => 'Oseltamivir'],
            ['name' => 'Zidovudine'],
            ['name' => 'Tenofovir'],
            ['name' => 'Lamivudine'],
            ['name' => 'Efavirenz'],
            ['name' => 'Nevirapine'],
            ['name' => 'Rifampicin'],
            ['name' => 'Isoniazid'],
            ['name' => 'Ethambutol'],
            ['name' => 'Pyrazinamide'],
            ['name' => 'Chloroquine'],
            ['name' => 'Artemether'],
            ['name' => 'Lumefantrine'],
            ['name' => 'Albendazole'],
            ['name' => 'Mebendazole'],
            ['name' => 'Praziquantel'],
            ['name' => 'Diazepam'],
            ['name' => 'Lorazepam'],
            ['name' => 'Clonazepam'],
            ['name' => 'Amitriptyline'],
            ['name' => 'Fluoxetine'],
            ['name' => 'Sertraline'],
            ['name' => 'Escitalopram'],
            ['name' => 'Haloperidol'],
            ['name' => 'Risperidone'],
            ['name' => 'Olanzapine'],
            ['name' => 'Quetiapine'],
            ['name' => 'Domperidone'],
            ['name' => 'Metoclopramide'],
            ['name' => 'Ondansetron'],
            ['name' => 'Cyclizine'],
            ['name' => 'Iron Supplement'],
            ['name' => 'Folic Acid'],
            ['name' => 'Vitamin B12'],
            ['name' => 'Calcium'],
            ['name' => 'Vitamin D'],
            ['name' => 'Magnesium'],
            ['name' => 'Zinc'],
            ['name' => 'ORS'],
            ['name' => 'ORS Sachet'],
            ['name' => 'Chlorpheniramine'],
            ['name' => 'Phenylephrine'],
            ['name' => 'Dextromethorphan'],
            ['name' => 'Codeine'],
            ['name' => 'Morphine'],
            ['name' => 'Tramadol'],
            ['name' => 'Diclofenac'],
            ['name' => 'Naproxen'],
            ['name' => 'Meloxicam'],
            ['name' => 'Celecoxib'],
            ['name' => 'Etoricoxib'],
            ['name' => 'Tetracycline'],
            ['name' => 'Doxycycline'],
            ['name' => 'Linezolid'],
        ];

        foreach ($medicines as &$medicine) {
            $medicine['id'] = (string) Str::uuid(); // optional, if your medicines use UUIDs
            $medicine['brand'] = $brands[array_rand($brands)];
            $medicine['medicine_category_id'] = $categoryIds[array_rand($categoryIds)];
            $medicine['created_at'] = now();
            $medicine['updated_at'] = now();
        }

        DB::table('medicines')->insert($medicines);
    }
}
