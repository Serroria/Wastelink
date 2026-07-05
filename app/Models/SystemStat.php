<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemStat extends Model
{
    protected $table = 'system_stats';

    protected $fillable = ['bank_sampah_cash', 'total_co2_saved', 'total_landfill_saved'];

    protected function casts(): array
    {
        return [
            'bank_sampah_cash' => 'decimal:2',
            'total_co2_saved' => 'decimal:2',
            'total_landfill_saved' => 'decimal:2',
        ];
    }
}
