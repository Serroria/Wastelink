<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Withdrawal extends Model
{
    protected $fillable = ['user_id', 'bank_name', 'account_number', 'account_name', 'points_amount', 'equivalent_rp', 'status'];

    protected function casts(): array
    {
        return [
            'points_amount' => 'integer',
            'equivalent_rp' => 'decimal:2',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
