<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        User::query()
            ->where(function ($query): void {
                $query->whereNull('username')
                    ->orWhere('username', '');
            })
            ->orderBy('id')
            ->each(function (User $user): void {
                $base = Str::slug(Str::before($user->email, '@'), '_');

                if ($base === '') {
                    $base = 'user_'.$user->id;
                }

                $username = $base;
                $counter = 1;

                while (User::where('username', $username)->where('id', '!=', $user->id)->exists()) {
                    $username = $base.'_'.$counter;
                    $counter++;
                }

                $user->update(['username' => $username]);
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
