<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationAndBankSampahAccountTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_user_can_register_as_industrial_buyer(): void
    {
        $response = $this->post(route('register'), [
            'name' => 'PT Pembeli Baru',
            'username' => 'pembeli_baru',
            'email' => 'pembeli-baru@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'pembeli',
        ]);

        $response->assertRedirect(route('pembeli.dashboard'));

        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'username' => 'pembeli_baru',
            'email' => 'pembeli-baru@example.com',
            'role' => 'pembeli',
        ]);
    }

    public function test_bank_sampah_can_create_another_bank_sampah_account(): void
    {
        $operator = User::factory()->create([
            'name' => 'Operator Bank Sampah',
            'username' => 'operator_bank_sampah',
            'role' => 'bank_sampah',
        ]);

        $response = $this->actingAs($operator)->post(route('bank-sampah.accounts.store'), [
            'name' => 'Cabang Bank Sampah Baru',
            'username' => 'bank_sampah_baru',
            'email' => 'bank-sampah-baru@example.com',
            'phone' => '081234567899',
            'address' => 'Jl. Bank Sampah No. 2',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('bank-sampah.dashboard'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'username' => 'bank_sampah_baru',
            'email' => 'bank-sampah-baru@example.com',
            'role' => 'bank_sampah',
            'point_balance' => 0,
            'cash_balance' => 0,
        ]);
    }

    public function test_non_bank_sampah_cannot_create_bank_sampah_account(): void
    {
        $warga = User::factory()->create([
            'username' => 'warga_biasa',
            'role' => 'warga',
        ]);

        $response = $this->actingAs($warga)->post(route('bank-sampah.accounts.store'), [
            'name' => 'Cabang Tidak Sah',
            'username' => 'bank_sampah_tidak_sah',
            'email' => 'bank-sampah-tidak-sah@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertForbidden();
        $this->assertDatabaseMissing('users', [
            'username' => 'bank_sampah_tidak_sah',
        ]);
    }
}
