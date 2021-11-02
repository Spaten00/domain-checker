<?php

namespace Database\Seeders;

use App\Models\Bill;
use App\Models\Contract;
use App\Models\Customer;
use App\Models\Domain;
use App\Models\Group;
use App\Models\Hosting;
use App\Models\RrpproxyEntry;
use App\Models\TanssEntry;
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
//        Group::factory(10)->create();
//        User::factory(10)->create();
//        Domain::factory()->create();
//        Customer::factory()->create();
//        TanssEntry::factory()->create();
//        RrpproxyEntry::factory()->create();
//        Contract::factory()->create();
//        Bill::factory()->create();
//        Domain::factory(10)->has(Contract::factory(5))->create();
//        Hosting::factory(10)->has(Contract::factory(5))->create();
//        Contract::factory(10)->has(Domain::factory())->has(Hosting::factory())->create();
        $domain = Domain::factory()->create();
        Customer::factory()->create();
        TanssEntry::factory()->create();
        RrpproxyEntry::factory()->create();
        $contract = Contract::factory()->create();
        Bill::factory()->create();

        $domain->contracts()->attach($contract);
    }
}
