<?php

namespace Tests\Unit;

use App\Models\Contract;
use App\Models\Customer;
use App\Models\Domain;
use App\Models\TanssEntry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;
use Illuminate\Database\Eloquent\Collection;

class DomainTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function domains_table_has_expected_columns()
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
        $this->assertSame(Schema::getColumnListing('domains'), $expectedColumns);
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
        $this->assertInstanceOf(Collection::class, $domain->contracts);

        $domain->contracts()->attach(Contract::factory()->create(['customer_id' => $customer->id]));
        $this->assertEquals(2, $domain->contracts()->count());

    }

    /** @test */
    public function a_domain_has_one_tanss_entry()
    {
        /** @var Domain $domain */
        $domain = Domain::factory()->create();
        Customer::factory()->create();
        /** @var TanssEntry $tanssEntry */
        $tanssEntry = TanssEntry::factory()->create();

        $this->assertEquals($tanssEntry->id, $domain->tanssEntry->id);
        $this->assertEquals(1, $domain->tanssEntry->count());
        $this->assertInstanceOf(TanssEntry::class, $domain->tanssEntry);
    }

    /** @test */
    public function a_domain_has_one_rrpproxy_entry()
    {

    }
}
