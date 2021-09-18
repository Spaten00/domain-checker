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
            'name',
        ];

        $this->assertTrue(Schema::hasColumns('domains', $expectedColumns));
        // check that no other columns are created
        $this->assertTrue(Schema::getColumnListing('domains') === $expectedColumns);
    }

    /** @test */
    public function a_domain_belongs_to_many_contracts()
    {
        /** @var Customer $customer */
        $customer = Customer::factory()->create();
        /** @var Contract $contract */
        $contract = Contract::factory()->create(['customer_id' => $customer->id]);
        /** @var Domain $domain */
        $domain = Domain::factory()->create();
        $domain->contracts()->attach($contract);

        $this->assertTrue($domain->contracts->contains($contract));
        $this->assertEquals(1, $domain->contracts()->count());
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $domain->contracts);

        $domain->contracts()->attach(Contract::factory()->create(['customer_id' => $customer->id]));
        $this->assertEquals(2, $domain->contracts()->count());

    }
}
