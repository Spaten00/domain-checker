<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Tino Linzenich
        User::create([
            'group_id' => 1,
            'first_name' => 'Tino',
            'last_name' => 'Linzenich',
            'email' => 'tn@aks-service.de',
            'password' => password_hash('12345678', 1),
            'remember_token' => null,
            'created_at' => now(),
            'email_verified_at' => now(),
        ]);

        // Marcel Bonjean
        User::create([
            'group_id' => 2,
            'first_name' => 'Marcel',
            'last_name' => 'Bonjean',
            'email' => 'mb@aks-service.de',
            'password' => password_hash('12345678', 1),
            'remember_token' => null,
            'created_at' => now(),
            'email_verified_at' => now(),
        ]);

        // Florian Schmitz
        User::create([
            'group_id' => 2,
            'first_name' => 'Florian',
            'last_name' => 'Schmitz',
            'email' => 'fs@aks-service.de',
            'password' => password_hash('12345678', 1),
            'remember_token' => null,
            'created_at' => now(),
            'email_verified_at' => now(),
        ]);
    }
}
