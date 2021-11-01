<?php

namespace Tests\Unit;

use App\Models\Bill;
use App\Models\Contract;
use App\Models\Customer;
use App\Models\Domain;
use App\Models\RrpproxyEntry;
use App\Models\TanssEntry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
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
    public function the_correct_class_and_text_for_the_badges_is_returned()
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

    /** @test */
    public function a_string_containing_html_to_create_a_badge_is_returned()
    {
        /** @var Domain $domain */
        $domain = Domain::factory()->create();
        Customer::factory()->create();
        TanssEntry::factory()->create();
        RrpproxyEntry::factory()->create();

        $domain->tanssEntry->contract_end = now()->addDays(31);
        $domain->rrpproxyEntry->contract_end = now()->addDays(31);
        $this->assertEquals('<span class="badge bg-success">OK</span>', $domain->getStatusBadge());
    }

    /** @test */
    public function it_can_be_checked_if_the_domain_has_a_customer()
    {
        /** @var Domain $domain */
        $domain = Domain::factory()->create();
        $this->assertFalse($domain->hasCustomer());
        $domain->delete();

        /** @var Domain $domain */
        $domain = Domain::factory()->create();
        Customer::factory()->create();
        TanssEntry::factory()->create();
        $this->assertTrue($domain->hasCustomer());
    }

    /** @test */
    public function the_customer_id_can_be_returned()
    {
        /** @var Domain $domain */
        $domain = Domain::factory()->create();
        /** @var Customer $customer */
        $customer = Customer::factory()->create();
        $this->assertNotEquals($customer->id, $domain->getCustomerId());
        $domain->delete();
        $customer->delete();

        /** @var Domain $domain */
        $domain = Domain::factory()->create();
        /** @var Customer $customer */
        $customer = Customer::factory()->create();
        TanssEntry::factory()->create();
        $this->assertEquals($customer->id, $domain->getCustomerId());
    }

    /** @test */
    public function the_correct_string_with_the_name_of_the_customer_or_a_badge_is_returned()
    {
        /** @var Domain $domain */
        $domain = Domain::factory()->create();
        /** @var Customer $customer */
        $customer = Customer::factory()->create();
        $tanssEntry = TanssEntry::factory()->create();
        $this->assertEquals($customer->name, $domain->getCustomer());
        $tanssEntry->delete();
        $domain->delete();
        $customer->delete();

        /** @var Domain $domain */
        $domain = Domain::factory()->create();
        $this->assertEquals('Kunde fehlt', $domain->getCustomer());
        $domain->delete();

        /** @var Domain $domain */
        $domain = Domain::factory()->create();
        $rrpproxyEntry = RrpproxyEntry::factory()->create();
        $domain->rrpproxyEntry->contract_end = now()->subDays(31);
        $this->assertEquals('Kunde fehlt', $domain->getCustomer());
        $rrpproxyEntry->delete();
        $domain->delete();

        /** @var Domain $domain */
        $domain = Domain::factory()->create();
        $rrpproxyEntry = RrpproxyEntry::factory()->create();
        $domain->rrpproxyEntry->contract_end = now()->addDays(31);
        $this->assertEquals('<span class="badge bg-danger">Kunde fehlt</span>', $domain->getCustomer());
        $rrpproxyEntry->delete();
        $domain->delete();
    }

    /** @test */
    public function the_correct_string_with_the_end_date_of_tanss_and_the_correct_badge_can_be_returned()
    {
        /** @var Domain $domain */
        $domain = Domain::factory()->create();
        $this->assertEquals('<span class="badge bg-danger">fehlt</span>', $domain->getTanssEnd());
        $domain->delete();

        /** @var Domain $domain */
        $domain = Domain::factory()->create();
        $rrpproxyEntry = RrpproxyEntry::factory()->create();
        $domain->rrpproxyEntry->contract_end = now()->subDays(31);
        $this->assertEquals('fehlt', $domain->getTanssEnd());
        $rrpproxyEntry->delete();
        $domain->delete();

        /** @var Domain $domain */
        $domain = Domain::factory()->create();
        $customer = Customer::factory()->create();
        /** @var TanssEntry $tanssEntry */
        $tanssEntry = TanssEntry::factory()->create();
        $tanssEntry->contract_end = now()->subDays(31);
        $tanssEntry->save();
        $this->assertEquals(Carbon::parse($tanssEntry->contract_end)->format('d-m-Y'), $domain->getTanssEnd());
        $tanssEntry->delete();
        $customer->delete();
        $domain->delete();

        /** @var Domain $domain */
        $domain = Domain::factory()->create();
        $customer = Customer::factory()->create();
        /** @var TanssEntry $tanssEntry */
        $tanssEntry = TanssEntry::factory()->create();
        $tanssEntry->contract_end = null;
        $tanssEntry->save();
        $this->assertEquals('fehlt', $domain->getTanssEnd());
        $tanssEntry->delete();
        $customer->delete();
        $domain->delete();

        /** @var Domain $domain */
        $domain = Domain::factory()->create();
        $customer = Customer::factory()->create();
        /** @var TanssEntry $tanssEntry */
        $tanssEntry = TanssEntry::factory()->create();
        $tanssEntry->contract_end = now()->addDays(31);
        $tanssEntry->save();
        $this->assertEquals(Carbon::parse($tanssEntry->contract_end)->format('d-m-Y'), $domain->getTanssEnd());
        $tanssEntry->delete();
        $customer->delete();
        $domain->delete();

        /** @var Domain $domain */
        $domain = Domain::factory()->create();
        $customer = Customer::factory()->create();
        /** @var TanssEntry $tanssEntry */
        $tanssEntry = TanssEntry::factory()->create();
        $tanssEntry->contract_end = now()->addDays(30);
        $tanssEntry->save();
        $expected = '<span class="badge bg-warning">'
            . Carbon::parse($tanssEntry->contract_end)->format('d-m-Y')
            . '</span>';
        $this->assertEquals($expected, $domain->getTanssEnd());
        $tanssEntry->delete();
        $customer->delete();
        $domain->delete();

        /** @var Domain $domain */
        $domain = Domain::factory()->create();
        $customer = Customer::factory()->create();
        /** @var TanssEntry $tanssEntry */
        $tanssEntry = TanssEntry::factory()->create();
        $tanssEntry->contract_end = now()->addDays(100);
        $tanssEntry->save();
        /** @var RrpproxyEntry $rrpproxyEntry */
        $rrpproxyEntry = RrpproxyEntry::factory()->create();
        $rrpproxyEntry->contract_end = now()->addDays(100);
        $rrpproxyEntry->save();
        $this->assertEquals(Carbon::parse($tanssEntry->contract_end)->format('d-m-Y'), $domain->getTanssEnd());
        $tanssEntry->delete();
        $rrpproxyEntry->delete();
        $customer->delete();
        $domain->delete();

        /** @var Domain $domain */
        $domain = Domain::factory()->create();
        $customer = Customer::factory()->create();
        /** @var TanssEntry $tanssEntry */
        $tanssEntry = TanssEntry::factory()->create();
        $tanssEntry->contract_end = now()->subDays(100);
        $tanssEntry->save();
        /** @var RrpproxyEntry $rrpproxyEntry */
        $rrpproxyEntry = RrpproxyEntry::factory()->create();
        $rrpproxyEntry->contract_end = now()->subDays(100);
        $rrpproxyEntry->save();
        $this->assertEquals(Carbon::parse($tanssEntry->contract_end)->format('d-m-Y'), $domain->getTanssEnd());
        $tanssEntry->delete();
        $rrpproxyEntry->delete();
        $customer->delete();
        $domain->delete();

        /** @var Domain $domain */
        $domain = Domain::factory()->create();
        $customer = Customer::factory()->create();
        /** @var TanssEntry $tanssEntry */
        $tanssEntry = TanssEntry::factory()->create();
        $tanssEntry->contract_end = now()->subDays(30);
        $tanssEntry->save();
        /** @var RrpproxyEntry $rrpproxyEntry */
        $rrpproxyEntry = RrpproxyEntry::factory()->create();
        $rrpproxyEntry->contract_end = now()->addDays(100);
        $rrpproxyEntry->save();
        $expected = '<span class="badge bg-danger">'
            . Carbon::parse($tanssEntry->contract_end)->format('d-m-Y')
            . '</span>';
        $this->assertEquals($expected, $domain->getTanssEnd());
    }

    /** @test */
    public function the_correct_string_with_the_end_date_of_rrpproxy_and_the_correct_badge_can_be_returned()
    {
        /** @var Domain $domain */
        $domain = Domain::factory()->create();
        $this->assertEquals('<span class="badge bg-danger">fehlt</span>', $domain->getRrpproxyEnd());
        $domain->delete();

        /** @var Domain $domain */
        $domain = Domain::factory()->create();
        /** @var RrpproxyEntry $rrpproxyEntry */
        $rrpproxyEntry = RrpproxyEntry::factory()->create();
        $this->assertEquals(Carbon::parse($rrpproxyEntry->contract_end)->format('d-m-Y'), $domain->getRrpproxyEnd());
    }

    /** @test */
    public function the_correct_string_with_the_renewal_date_of_rrpproxy_and_the_correct_badge_can_be_returned()
    {
        /** @var Domain $domain */
        $domain = Domain::factory()->create();
        $this->assertEquals('<span class="badge bg-danger">fehlt</span>', $domain->getRrpproxyRenewal());
        $domain->delete();

        /** @var Domain $domain */
        $domain = Domain::factory()->create();
        /** @var RrpproxyEntry $rrpproxyEntry */
        $rrpproxyEntry = RrpproxyEntry::factory()->create();
        $this->assertEquals(Carbon::parse($rrpproxyEntry->contract_renewal)->format('d-m-Y'), $domain->getRrpproxyRenewal());
    }

    /** @test */
    public function the_contract_number_or_an_empty_string_is_returned()
    {
        /** @var Domain $domain */
        $domain = Domain::factory()->create();
        $this->assertEquals('', $domain->getContractNumber());
        $domain->delete();

        Customer::factory()->create();
        /** @var Contract $contract */
        $contract = Contract::factory()->create();
        /** @var Domain $domain */
        $domain = Domain::factory()->create();
        $domain->contracts->add($contract);
        $this->assertEquals($contract->contract_number, $domain->getContractNumber());
    }

    /** @test */
    public function the_contract_id_or_an_empty_string_is_returned()
    {
        /** @var Domain $domain */
        $domain = Domain::factory()->create();
        $this->assertEquals('', $domain->getContractNumber());
        $domain->delete();

        Customer::factory()->create();
        /** @var Contract $contract */
        $contract = Contract::factory()->create();
        /** @var Domain $domain */
        $domain = Domain::factory()->create();
        $domain->contracts->add($contract);
        $this->assertEquals($contract->getKey(), $domain->getContractId());
    }

    /** @test */
    public function it_can_be_checked_if_the_domain_has_a_contract()
    {
        /** @var Domain $domain */
        $domain = Domain::factory()->create();
        $this->assertFalse($domain->hasContract());
        $domain->delete();

        Customer::factory()->create();
        /** @var Contract $contract */
        $contract = Contract::factory()->create();
        /** @var Domain $domain */
        $domain = Domain::factory()->create();
        $domain->contracts->add($contract);
        $this->assertTrue($domain->hasContract());
    }

    /** @test */
    public function it_can_be_checked_if_the_domain_has_a_bill()
    {
        /** @var Domain $domain */
        $domain = Domain::factory()->create();
        $this->assertFalse($domain->hasBill());
        $domain->delete();

        Customer::factory()->create();
        /** @var Contract $contract */
        $contract = Contract::factory()->create();
        Bill::factory()->create();
        /** @var Domain $domain */
        $domain = Domain::factory()->create();
        $domain->contracts->add($contract);
        $this->assertTrue($domain->hasBill());
    }

    /** @test */
    public function the_bill_number_from_the_last_bill_or_an_empty_string_is_returned()
    {
        /** @var Domain $domain */
        $domain = Domain::factory()->create();
        $this->assertEquals('', $domain->getLastBillNumber());
        $domain->delete();

        Customer::factory()->create();
        /** @var Contract $contract */
        $contract = Contract::factory()->create();
        /** @var Bill $bill */
        $bill = Bill::factory()->create();
        /** @var Domain $domain */
        $domain = Domain::factory()->create();
        $domain->contracts->add($contract);
        $this->assertEquals($bill->bill_number, $domain->getLastBillNumber());
    }

    /** @test */
    public function the_date_from_the_last_bill_or_an_empty_string_is_returned()
    {
        /** @var Domain $domain */
        $domain = Domain::factory()->create();
        $this->assertEquals('', $domain->getLastBillDate());
        $domain->delete();

        Customer::factory()->create();
        /** @var Contract $contract */
        $contract = Contract::factory()->create();
        /** @var Bill $bill */
        $bill = Bill::factory()->create();
        /** @var Domain $domain */
        $domain = Domain::factory()->create();
        $domain->contracts->add($contract);
        $this->assertEquals(Carbon::parse($bill->date)->format('d-m-Y'), $domain->getLastBillDate());
    }

    /** @test */
    public function the_id_from_the_last_bill_or_an_empty_string_is_returned()
    {
        /** @var Domain $domain */
        $domain = Domain::factory()->create();
        $this->assertEquals('', $domain->getLastBillId());
        $domain->delete();

        Customer::factory()->create();
        /** @var Contract $contract */
        $contract = Contract::factory()->create();
        /** @var Bill $bill */
        $bill = Bill::factory()->create();
        /** @var Domain $domain */
        $domain = Domain::factory()->create();
        $domain->contracts->add($contract);
        $this->assertEquals($bill->getKey(), $domain->getLastBillId());
    }
}
