<?php

namespace Tests\Unit;

use App\Models\Contract;
use App\Models\Customer;
use App\Models\Domain;
use App\Models\Hosting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ContractTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function contract_database_has_expected_columns()
    {
        $expectedColumns = [
            'id',
            'created_at',
            'updated_at',
            'deleted_at',
            'customer_id',
            'contract_number',
        ];

        $this->assertTrue(Schema::hasColumns('contracts', $expectedColumns));
        // check that no other columns are created
        $this->assertTrue(Schema::getColumnListing('contracts') === $expectedColumns);
    }

    /** @test */
    public function a_contract_belongs_to_a_user()
    {
        /** @var Customer $customer */
        $customer = Customer::factory()->create();
        /** @var Contract $contract */
        $contract = Contract::factory()->create(['customer_id' => $customer->id]);

        $this->assertEquals(1, $contract->customer->count());
        $this->assertInstanceOf(Customer::class, $contract->customer);
    }

    /** @test */
    public function a_contract_has_many_domains()
    {
        /** @var Customer $customer */
        $customer = Customer::factory()->create();
        /** @var Contract $contract */
        $contract = Contract::factory()->create(['customer_id' => $customer->id]);
        /** @var Domain $domain */
        $domain = Domain::factory()->create(['contract_id' => $contract->id]);

        $this->assertTrue($contract->domains->contains($domain));
        $this->assertEquals(1, $contract->domains->count());
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $contract->domains);
    }

    /** @test */
    public function a_contract_has_many_hostings()
    {
        /** @var Customer $customer */
        $customer = Customer::factory()->create();
        /** @var Contract $contract */
        $contract = Contract::factory()->create(['customer_id' => $customer->id]);
        /** @var Hosting $hosting */
        $hosting = Hosting::factory()->create(['contract_id' => $contract->id]);

        $this->assertTrue($contract->hostings->contains($hosting));
        $this->assertEquals(1, $contract->hostings->count());
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $contract->hostings);
    }
}