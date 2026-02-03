<?php

require 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$app = require 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

$count = App\Models\InventoryItem::count();
echo "Inventory items count: " . $count . "\n";

if ($count > 0) {
    $items = App\Models\InventoryItem::all();
    foreach ($items as $item) {
        echo "  " . $item->id . ": " . $item->name . " (qty: " . $item->quantity . ", status: " . $item->status . ")\n";
    }
}

