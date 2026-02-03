<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryItem extends Model
{
    protected $fillable = [
        'name',
        'sku',
        'category',
        'quantity',
        'unit',
        'min_level',
        'status',
        'notes',
    ];
}
