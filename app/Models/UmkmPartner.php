<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UmkmPartner extends Model
{
    protected $fillable = ['user_id', 'store_name', 'category', 'address', 'description'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function products()
    {
        return $this->hasMany(UmkmProduct::class);
    }

    public function settlements()
    {
        return $this->hasMany(Settlement::class);
    }
}
