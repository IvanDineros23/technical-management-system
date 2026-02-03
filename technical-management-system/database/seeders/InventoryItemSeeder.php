<?php

namespace Database\Seeders;

use App\Models\InventoryItem;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InventoryItemSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $items = [
            [
                'name' => 'Calibration Fluid',
                'sku' => 'CAL-FL-001',
                'category' => 'Supplies',
                'quantity' => 45,
                'unit' => 'units',
                'min_level' => 20,
                'status' => 'normal',
                'notes' => 'General calibration supply',
            ],
            [
                'name' => 'Sensor Cleaning Kit',
                'sku' => 'CLN-KT-002',
                'category' => 'Consumables',
                'quantity' => 8,
                'unit' => 'kits',
                'min_level' => 10,
                'status' => 'low',
                'notes' => 'Reorder when low',
            ],
            [
                'name' => 'Probe Tips (Pack)',
                'sku' => 'PRB-TP-010',
                'category' => 'Accessories',
                'quantity' => 120,
                'unit' => 'packs',
                'min_level' => 30,
                'status' => 'normal',
                'notes' => 'Compatible with standard probes',
            ],
            [
                'name' => 'Thermal Paste',
                'sku' => 'THR-PS-004',
                'category' => 'Supplies',
                'quantity' => 0,
                'unit' => 'tubes',
                'min_level' => 5,
                'status' => 'out',
                'notes' => 'Urgent restock needed',
            ],
            [
                'name' => 'Safety Gloves',
                'sku' => 'SFT-GL-003',
                'category' => 'Safety',
                'quantity' => 18,
                'unit' => 'pairs',
                'min_level' => 15,
                'status' => 'normal',
                'notes' => 'Medium/Large sizes',
            ],
            [
                'name' => 'Label Stickers',
                'sku' => 'LBL-ST-007',
                'category' => 'Office',
                'quantity' => 5,
                'unit' => 'rolls',
                'min_level' => 6,
                'status' => 'low',
                'notes' => 'Used for equipment labels',
            ],
            [
                'name' => 'Battery Pack AA',
                'sku' => 'BAT-AA-012',
                'category' => 'Power',
                'quantity' => 60,
                'unit' => 'packs',
                'min_level' => 20,
                'status' => 'normal',
                'notes' => 'For handheld devices',
            ],
            [
                'name' => 'Isopropyl Alcohol',
                'sku' => 'ISO-AL-011',
                'category' => 'Cleaning',
                'quantity' => 9,
                'unit' => 'bottles',
                'min_level' => 10,
                'status' => 'low',
                'notes' => '70% solution',
            ],
        ];

        foreach ($items as $item) {
            InventoryItem::create($item);
        }
    }
}
