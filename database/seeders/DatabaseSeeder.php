<?php

namespace Database\Seeders;

use App\Models\Contract;
use App\Models\Customer;
use App\Models\Domain;
use App\Models\Group;
use App\Models\Hosting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            GroupSeeder::class,
            UserSeeder::class,
        ]);

        // TODO delete factories before deploy
        Group::factory(10)->create();
        User::factory(10)->create();
        Customer::factory(10)->create();
        Contract::factory(10)->create();
        Domain::factory(10)->create();
        Hosting::factory(10)->create();
    }
}
