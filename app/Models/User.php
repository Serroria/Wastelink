<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'username', 'email', 'password', 'role', 'phone', 'address', 'profile_photo', 'point_balance', 'cash_balance', 'google_id',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'point_balance' => 'integer',
            'cash_balance' => 'decimal:2',
        ];
    }

    public function deposits()
    {
        return $this->hasMany(WasteDeposit::class);
    }

    public function verifiedDeposits()
    {
        return $this->hasMany(WasteDeposit::class, 'collector_id');
    }

    public function umkmPartner()
    {
        return $this->hasOne(UmkmPartner::class);
    }

    public function vouchers()
    {
        return $this->hasMany(Voucher::class);
    }

    public function withdrawals()
    {
        return $this->hasMany(Withdrawal::class);
    }

    public function profilePhotoUrl(): ?string
    {
        if (! $this->profile_photo) {
            return null;
        }

        return '/storage/'.$this->profile_photo;
    }

    public function initials(): string
    {
        $nameParts = explode(' ', $this->name);

        return strtoupper(substr($nameParts[0], 0, 1).(isset($nameParts[1]) ? substr($nameParts[1], 0, 1) : ''));
    }
}
