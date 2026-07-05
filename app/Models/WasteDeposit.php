<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WasteDeposit extends Model
{
    protected $fillable = ['user_id', 'collector_id', 'status', 'collection_method', 'schedule_date', 'address', 'weight_details', 'total_points', 'photo_proof', 'notes'];

    protected function casts(): array
    {
        return [
            'weight_details' => 'array',
            'total_points' => 'integer',
            'schedule_date' => 'date',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function collector()
    {
        return $this->belongsTo(User::class, 'collector_id');
    }
}
