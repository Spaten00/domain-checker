<?php

namespace Tests\Unit;

use App\Models\Contract;
use App\Models\Customer;
use App\Models\Domain;
use App\Models\RrpproxyEntry;
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
        /** @var Domain $domain */
        $domain = Domain::factory()->create();
        /** @var RrpproxyEntry $rrpproxyEntry */
        $rrpproxyEntry = RrpproxyEntry::factory()->create();

        $this->assertEquals($rrpproxyEntry->id, $domain->rrpproxyEntry->id);
        $this->assertEquals(1, $domain->rrpproxyEntry->count());
        $this->assertInstanceOf(RrpproxyEntry::class, $domain->rrpproxyEntry);
    }

    /** @test */
    public function a_domain_can_be_created()
    {
        $domain = Domain::createDomain('test.de');
        $this->assertModelExists($domain);
    }

    /** @test */
    public function a_domain_is_not_created_when_it_already_exists()
    {
        Domain::createDomain('test.de');
        $this->assertEquals(1, Domain::all()->count());

        Domain::createDomain('test.de');
        $this->assertEquals(1, Domain::all()->count());
    }

    /** @test */
    public function it_can_be_checked_if_a_tanss_entry_is_present()
    {
        /** @var Domain $domain */
        $domain = Domain::factory()->create();
        Customer::factory()->create();
        TanssEntry::factory()->create();
        $this->assertFalse($domain->hasNoTanss());

        /** @var Domain $newDomain */
        $newDomain = Domain::factory()->create();
        $this->assertTrue($newDomain->hasNoTanss());
    }

    /** @test */
    public function it_can_be_checked_if_a_rrpproxy_entry_is_present()
    {
        /** @var Domain $domain */
        $domain = Domain::factory()->create();
        RrpproxyEntry::factory()->create();
        $this->assertFalse($domain->hasNoRrpproxy());

        /** @var Domain $newDomain */
        $newDomain = Domain::factory()->create();
        $this->assertTrue($newDomain->hasNoRrpproxy());
    }

    /** @test */
    public function it_can_be_checked_if_no_entries_are_present()
    {
        /** @var Domain $domain */
        $domain = Domain::factory()->create();
        Customer::factory()->create();
        TanssEntry::factory()->create();
        RrpproxyEntry::factory()->create();
        $this->assertFalse($domain->hasNoEntries());

        /** @var Domain $newDomain */
        $newDomain = Domain::factory()->create();
        $this->assertTrue($newDomain->hasNoEntries());
    }

    /** @test */
    public function it_can_be_checked_if_its_tanss_entry_is_expired()
    {
        /** @var Domain $domain */
        $domain = Domain::factory()->create();
        Customer::factory()->create();
        /** @var TanssEntry $expiredEntry */
        $expiredEntry = TanssEntry::factory()->create();

        $expiredEntry->contract_end = "2015-12-09";
        $expiredEntry->save();
//        $this->assertTrue($domain->hasTanssExpired());

        $expiredEntry->contract_end = "2100-12-09";
        $expiredEntry->save();
//        dump($expiredEntry);
        $this->assertFalse($domain->hasTanssExpired());
        // TODO
    }
}
