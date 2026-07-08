<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WasteListing extends Model
{
    protected $fillable = ['title', 'description', 'weight_details'=> 'array', 'total_price', 'status', 'buyer_id', 'sold_at'];

    protected function casts(): array
    {
        return [
            'weight_details' => 'array',
            'total_price' => 'decimal:2',
            'sold_at' => 'datetime',
        ];
    }

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }


}
