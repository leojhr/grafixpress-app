<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_name',
        'description',
        'quantity',
        'supply_cost',
        'sale_price',
        'is_service'
    ];
}
