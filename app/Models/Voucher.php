<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    protected $fillable = ['user_id', 'umkm_product_id', 'code', 'points_spent', 'status', 'used_at', 'claimed_at'];

    protected function casts(): array
    {
        return [
            'points_spent' => 'integer',
            'used_at' => 'datetime',
            'claimed_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(UmkmProduct::class, 'umkm_product_id');
    }
}
