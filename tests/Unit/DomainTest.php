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
        TanssEntry::factory()->create();

        $domain->tanssEntry->contract_end = "2015-12-09";
        $this->assertTrue($domain->hasTanssExpired());

        $domain->tanssEntry->contract_end = "2100-12-09";
        $this->assertFalse($domain->hasTanssExpired());
    }

    /** @test */
    public function it_can_be_checked_if_its_rrpproxy_entry_is_expired()
    {
        /** @var Domain $domain */
        $domain = Domain::factory()->create();
        RrpproxyEntry::factory()->create();

        $domain->rrpproxyEntry->contract_end = "2015-12-09";
        $this->assertTrue($domain->hasRrpproxyExpired());

        $domain->rrpproxyEntry->contract_end = "2100-12-09";
        $this->assertFalse($domain->hasRrpproxyExpired());
    }

    /** @test */
    public function it_can_be_checked_if_both_entries_are_expired()
    {
        /** @var Domain $domain */
        $domain = Domain::factory()->create();
        Customer::factory()->create();
        TanssEntry::factory()->create();
        RrpproxyEntry::factory()->create();

        $domain->tanssEntry->contract_end = "2015-12-09";
        $domain->rrpproxyEntry->contract_end = "2015-12-09";
        $this->assertTrue($domain->hasBothExpired());

        $domain->tanssEntry->contract_end = "2100-12-09";
        $this->assertFalse($domain->hasBothExpired());

        $domain->rrpproxyEntry->contract_end = "2100-12-09";
        $this->assertFalse($domain->hasBothExpired());

        $domain->tanssEntry->contract_end = "2015-12-09";
        $this->assertFalse($domain->hasBothExpired());
    }

    /** @test */
    public function it_can_be_checked_if_either_entry_is_expired()
    {
        /** @var Domain $domain */
        $domain = Domain::factory()->create();
        Customer::factory()->create();
        TanssEntry::factory()->create();
        RrpproxyEntry::factory()->create();

        $domain->tanssEntry->contract_end = "2015-12-09";
        $domain->rrpproxyEntry->contract_end = "2015-12-09";
        $this->assertTrue($domain->hasEitherExpired());

        $domain->tanssEntry->contract_end = "2100-12-09";
        $this->assertTrue($domain->hasEitherExpired());

        $domain->rrpproxyEntry->contract_end = "2100-12-09";
        $this->assertFalse($domain->hasEitherExpired());

        $domain->tanssEntry->contract_end = "2015-12-09";
        $this->assertTrue($domain->hasEitherExpired());
    }

    /** @test */
    public function it_can_be_checked_if_either_entry_will_expire_soon()
    {
        /** @var Domain $domain */
        $domain = Domain::factory()->create();
        Customer::factory()->create();
        TanssEntry::factory()->create();
        RrpproxyEntry::factory()->create();

        $domain->tanssEntry->contract_end = now()->addDay();
        $domain->rrpproxyEntry->contract_end = now()->addDay();
        $this->assertTrue($domain->hasEitherExpireSoon());

        $domain->tanssEntry->contract_end = now()->addDays(30);
        $domain->rrpproxyEntry->contract_end = now()->addDays(30);
        $this->assertTrue($domain->hasEitherExpireSoon());

        $domain->tanssEntry->contract_end = now()->addDays(31);
        $domain->rrpproxyEntry->contract_end = now()->addDays(31);
        $this->assertFalse($domain->hasEitherExpireSoon());

        $domain->tanssEntry->contract_end = now()->addDays(5);
        $domain->rrpproxyEntry->contract_end = now()->addDays(31);
        $this->assertTrue($domain->hasEitherExpireSoon());

        $domain->tanssEntry->contract_end = now()->addDays(31);
        $domain->rrpproxyEntry->contract_end = now()->addDays(5);
        $this->assertTrue($domain->hasEitherExpireSoon());
    }

    /** @test */
    public function the_correct_class_and_text_is_returned()
    {
        /** @var Domain $domain */
        $domain = Domain::factory()->create();
        $this->assertEquals(['badge bg-info', 'Keine Einträge'], $domain->getClassAndText());
        $domain->delete();

        /** @var Domain $domain */
        $domain = Domain::factory()->create();
        $rrpproxy = RrpproxyEntry::factory()->create();
        $domain->rrpproxyEntry->contract_end = now()->addDays(200);
        $this->assertEquals(['badge bg-danger', 'TANSS fehlt'], $domain->getClassAndText());

        $domain->rrpproxyEntry->contract_end = now()->subDays(200);
        $this->assertEquals(['badge bg-success', 'OK'], $domain->getClassAndText());
        $rrpproxy->delete();
        $domain->delete();

        /** @var Domain $domain */
        $domain = Domain::factory()->create();
        $customer = Customer::factory()->create();
        $tanssentry = TanssEntry::factory()->create();
        $domain->tanssEntry->contract_end = now()->addDays(200);
        $this->assertEquals(['badge bg-danger', 'RRPproxy fehlt'], $domain->getClassAndText());

        $domain->tanssEntry->contract_end = now()->subDays(200);
        $this->assertEquals(['badge bg-danger', 'Kein Ablaufdatum hinterlegt'], $domain->getClassAndText());
        $tanssentry->delete();
        $customer->delete();
        $domain->delete();

        /** @var Domain $domain */
        $domain = Domain::factory()->create();
        Customer::factory()->create();
        TanssEntry::factory()->create();
        RrpproxyEntry::factory()->create();
        $domain->tanssEntry->contract_end = now()->subDays(200);
        $domain->rrpproxyEntry->contract_end = now()->subDays(200);
        $this->assertEquals(['badge bg-success', 'OK'], $domain->getClassAndText());

        $domain->tanssEntry->contract_end = now()->addDays(200);
        $domain->rrpproxyEntry->contract_end = now()->subDays(200);
        $this->assertEquals(['badge bg-danger', 'Vertrag ausgelaufen'], $domain->getClassAndText());

        $domain->tanssEntry->contract_end = now()->subDays(200);
        $domain->rrpproxyEntry->contract_end = now()->addDays(200);
        $this->assertEquals(['badge bg-danger', 'Vertrag ausgelaufen'], $domain->getClassAndText());

        $domain->tanssEntry->contract_end = now()->addDays(30);
        $domain->rrpproxyEntry->contract_end = now()->addDays(30);
        $this->assertEquals(['badge bg-warning', 'Vertrag läuft aus'], $domain->getClassAndText());

        $domain->tanssEntry->contract_end = now()->addDays(31);
        $domain->rrpproxyEntry->contract_end = now()->addDays(30);
        $this->assertEquals(['badge bg-warning', 'Vertrag läuft aus'], $domain->getClassAndText());

        $domain->tanssEntry->contract_end = now()->addDays(30);
        $domain->rrpproxyEntry->contract_end = now()->addDays(31);
        $this->assertEquals(['badge bg-warning', 'Vertrag läuft aus'], $domain->getClassAndText());

        $domain->tanssEntry->contract_end = now()->addDays(31);
        $domain->rrpproxyEntry->contract_end = now()->addDays(31);
        $this->assertEquals(['badge bg-success', 'OK'], $domain->getClassAndText());
    }
}
