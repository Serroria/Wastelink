<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Settlement extends Model
{
    protected $fillable = ['umkm_partner_id', 'total_amount', 'voucher_ids', 'status', 'paid_at'];

    protected function casts(): array
    {
        return [
            'total_amount' => 'decimal:2',
            'voucher_ids' => 'array',
            'paid_at' => 'datetime',
        ];
    }

    public function partner()
    {
        return $this->belongsTo(UmkmPartner::class, 'umkm_partner_id');
    }
}
