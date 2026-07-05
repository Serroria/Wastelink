<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WalletTransaction extends Model
{
    protected $fillable = [
        'user_id',
        'transaction_type',
        'biller_name',
        'account_number',
        'points_spent',
        'nominal_rp',
        'ref_number',
        'status',
    ];

    protected $casts = [
        'points_spent' => 'integer',
        'nominal_rp' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
