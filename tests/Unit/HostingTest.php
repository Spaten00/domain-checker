<?php

namespace Tests\Unit;

use App\Models\Contract;
use App\Models\Customer;
use App\Models\Hosting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class HostingTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function hostings_database_has_expected_columns()
    {
        $expectedColumns = [
            'id',
            'created_at',
            'updated_at',
            'deleted_at',
            'type',
        ];

        $this->assertTrue(Schema::hasColumns('hostings', $expectedColumns));
        // check that no other columns are created
        $this->assertTrue(Schema::getColumnListing('hostings') === $expectedColumns);
    }

    /** @test */
    public function a_hosting_belongs_to_a_contract()
    {
        /** @var Customer $customer */
        $customer = Customer::factory()->create();
        /** @var Contract $contract */
        $contract = Contract::factory()->create(['customer_id' => $customer->id]);
        /** @var Hosting $hosting */
        $hosting = Hosting::factory()->create();
        $hosting->contracts()->attach($contract);

        $this->assertTrue($hosting->contracts->contains($contract));
        $this->assertEquals(1, $hosting->contracts()->count());
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $hosting->contracts);

        $hosting->contracts()->attach(Contract::factory()->create(['customer_id' => $customer->id]));
        $this->assertEquals(2, $hosting->contracts()->count());
    }
}
