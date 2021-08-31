<?php

namespace Tests\Unit;

use App\Models\Contract;
use App\Models\Customer;
use App\Models\Domain;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DomainTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function domains_database_has_expected_columns()
    {
        $expectedColumns = [
            'id',
            'created_at',
            'updated_at',
            'deleted_at',
            'contract_id',
            'domain_name',
        ];

        $this->assertTrue(Schema::hasColumns('domains', $expectedColumns));
        // check that no other columns are created
        $this->assertTrue(Schema::getColumnListing('domains') === $expectedColumns);
    }

    /** @test */
    public function a_domain_belongs_to_a_contract()
    {
        /** @var Customer $customer */
        $customer = Customer::factory()->create();
        /** @var Contract $contract */
        $contract = Contract::factory()->create(['customer_id' => $customer->id]);
        /** @var Domain $domain */
        $domain = Domain::factory()->create(['contract_id' => $contract->id]);

        $this->assertEquals(1, $domain->contract->count());
        $this->assertInstanceOf(Contract::class, $domain->contract);
    }
}
