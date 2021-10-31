<?php

namespace Tests\Unit;

use App\Models\Customer;
use App\Models\Domain;
use App\Models\TanssEntry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class TanssEntryTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function tanss_entries_table_has_expected_columns()
    {
        $expectedColumns = [
            'id',
            'created_at',
            'updated_at',
            'deleted_at',
            'domain_id',
            'customer_id',
            'external_id',
            'provider_name',
            'contract_start',
            'contract_end',
        ];
        $this->assertTrue(Schema::hasColumns('tanss_entries', $expectedColumns));
        // check that no other columns are created
        $this->assertSame(Schema::getColumnListing('tanss_entries'), $expectedColumns);
    }

    /** @test */
    public function a_tanss_entry_has_one_customer()
    {
        Domain::factory()->create();
        /** @var Customer $customer */
        $customer = Customer::factory()->create();
        /** @var TanssEntry $tanssEntry */
        $tanssEntry = TanssEntry::factory()->create();

        $this->assertEquals($customer->id, $tanssEntry->customer->id);
        $this->assertEquals(1, $tanssEntry->customer->count());
        $this->assertInstanceOf(Customer::class, $tanssEntry->customer);
    }

    /** @test */
    public function a_tanss_entry_has_one_domain()
    {
        /** @var Domain $domain */
        $domain = Domain::factory()->create();
        Customer::factory()->create();
        /** @var TanssEntry $tanssEntry */
        $tanssEntry = TanssEntry::factory()->create();

        $this->assertEquals($domain->id, $tanssEntry->domain->id);
        $this->assertEquals(1, $tanssEntry->domain->count());
        $this->assertInstanceOf(Domain::class, $tanssEntry->domain);
    }

    /** @test */
    public function it_can_be_checked_if_the_entry_is_expired()
    {
        $expiredEntry = new TanssEntry();
        $expiredEntry->contract_end = "2015-12-09";
        self::assertTrue($expiredEntry->isExpired());

        $aliveEntry = new TanssEntry();
        $aliveEntry->contract_end = "2122-12-09";
        self::assertFalse($aliveEntry->isExpired());
    }

    /** @test */
    public function it_can_be_checked_if_the_entry_will_expire_soon()
    {
        $expiringEntry = new TanssEntry();
        $expiringEntry->contract_end = now()->addDays(1);
        self::assertTrue($expiringEntry->willExpireSoon());

        $aliveEntry = new TanssEntry();
        $aliveEntry->contract_end = now()->addDays(31);
        self::assertFalse($aliveEntry->willExpireSoon());
    }

    /** @test */
    public function a_tanss_entry_can_be_created()
    {
        $entry = [
            'externalId' => '1',
            'customerId' => '100000',
            'customerName' => 'aks Service GmbH',
            'domain' => 'aks-service.de',
            'providerName' => 'aks Service GmbH',
            'tanssContractStart' => '2013-07-20',
            'tanssContractEnd' => '2015-05-21',
        ];
        $customer = Customer::createCustomer($entry['customerId'], $entry['customerName']);
        $domain = Domain::createDomain($entry['domain']);
        $model = TanssEntry::createTanssEntry($entry, $customer, $domain);

        $this->assertModelExists($model);
        $this->assertEquals($model->id, TanssEntry::first()->id);
        $this->assertEquals($model->customer->id, TanssEntry::first()->customer->id);
        $this->assertEquals($model->domain->id, TanssEntry::first()->domain->id);

    }

    /** @test */
    public function a_tanss_entry_can_be_updated()
    {

    }
}
