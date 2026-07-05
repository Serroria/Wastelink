<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WasteType extends Model
{
    protected $fillable = ['name', 'icon', 'unit', 'points_per_kg', 'price_per_kg'];

    protected function casts(): array
    {
        return [
            'points_per_kg' => 'integer',
            'price_per_kg' => 'decimal:2',
        ];
    }
}
