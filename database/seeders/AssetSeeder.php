<?php

namespace Database\Seeders;

use App\Models\Asset;
use Illuminate\Database\Seeder;

class AssetSeeder extends Seeder
{
    public function run(): void
    {
        Asset::create([
            'asset_tag' => 'M-26-001',
            'name' => 'Laptop Acer Aspire Go 14',
            'person_in_charge' => null,
            'purchase_date' => '2026-02-06',
            'category' => 'Mobile',
            'status' => 'available',
            'condition' => 'Baik',
            'description' => 'masih berfungsi',
        ]);

        Asset::create([
            'asset_tag' => 'M-26-002',
            'name' => 'Laptop Dell Inspiron 1990',
            'person_in_charge' => 'Bang Alam',
            'purchase_date' => '2026-02-06',
            'category' => 'Mobile',
            'status' => 'maintenance',
            'condition' => 'Rusak Berat',
            'description' => 'layar patah',
        ]);

        Asset::create([
            'asset_tag' => 'M-26-003',
            'name' => 'Mouse Logitech B100',
            'person_in_charge' => 'Bang Alam',
            'purchase_date' => '2026-02-07',
            'category' => 'Mobile',
            'status' => 'in_use',
            'condition' => 'Rusak Ringan',
            'description' => 'casan hilang'
        ]);

    }
}