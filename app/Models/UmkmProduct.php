<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UmkmProduct extends Model
{
    protected $fillable = ['umkm_partner_id', 'name', 'description', 'points_cost', 'price_value', 'stock'];

    protected function casts(): array
    {
        return [
            'points_cost' => 'integer',
            'price_value' => 'decimal:2',
            'stock' => 'integer',
        ];
    }

    public function partner()
    {
        return $this->belongsTo(UmkmPartner::class, 'umkm_partner_id');
    }
}
