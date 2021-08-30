<?php

namespace Database\Seeders;

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
        User::create([
            'group_id' => 1,
            'name' => "Tino Linzenich",
            'email' => "tn@aks-service.de",
            'password' => password_hash('12345678', 1),
            'remember_token' => null,
            'created_at' => now(),
            'email_verified_at' => now(),
        ]);
    }
}
