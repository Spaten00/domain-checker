<?php

namespace Tests\Unit;


use App\Models\Domain;
use App\Models\RrpproxyEntry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class RRPproxyEntryTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function rrpproxy_entries_table_has_expected_columns()
    {
        $expectedColumns = [
            'id',
            'created_at',
            'updated_at',
            'deleted_at',
            'domain_id',
            'contract_start',
            'contract_end',
            'contract_renewal',
        ];
        $this->assertTrue(Schema::hasColumns('rrpproxy_entries', $expectedColumns));
        // check that no other columns are created
        $this->assertSame(Schema::getColumnListing('rrpproxy_entries'), $expectedColumns);
    }

    /** @test */
    public function a_rrpproxy_entry_has_one_domain()
    {
        /** @var Domain $domain */
        $domain = Domain::factory()->create();
        /** @var RRPproxyEntry $rrpproxyEntry */
        $rrpproxyEntry = RRPproxyEntry::factory()->create();

        $this->assertEquals($domain->id, $rrpproxyEntry->domain->id);
        $this->assertEquals(1, $rrpproxyEntry->domain->count());
        $this->assertInstanceOf(Domain::class, $rrpproxyEntry->domain);
    }

    /** @test */
    public function it_can_be_checked_if_the_entry_is_expired()
    {
        $expiredEntry = new RrpproxyEntry();
        $expiredEntry->contract_end = "2015-12-09";
        self::assertTrue($expiredEntry->isExpired());

        $aliveEntry = new RrpproxyEntry();
        $aliveEntry->contract_end = "2122-12-09";
        self::assertFalse($aliveEntry->isExpired());
    }

    /** @test */
    public function it_can_be_checked_if_the_entry_will_expire_soon()
    {
        $expiringEntry = new RrpproxyEntry();
        $expiringEntry->contract_end = now()->addDays(1);
        self::assertTrue($expiringEntry->willExpireSoon());

        $aliveEntry = new RrpproxyEntry();
        $aliveEntry->contract_end = now()->addDays(31);
        self::assertFalse($aliveEntry->willExpireSoon());
    }

    /** @test */
    public function a_rrpproxy_entry_can_be_created()
    {
        $entry = [
            'domain' => 'aks-service.de',
            'rrpproxyContractStart' => '2013-07-20',
            'rrpproxyContractEnd' => '2015-05-21',
            'rrpproxyContractRenewal' => '2015-05-20',
        ];
        $domain = Domain::createDomain($entry['domain']);
        $model = RrpproxyEntry::createOrUpdateRrpproxyEntry($entry, $domain);

        $this->assertModelExists($model);
        $this->assertEquals($model->id, RrpproxyEntry::first()->id);
        $this->assertEquals($model->domain->id, RrpproxyEntry::first()->domain->id);
    }

    /** @test */
    public function a_rrpproxy_entry_can_be_updated()
    {
        $entry = [
            'domain' => 'aks-service.de',
            'rrpproxyContractStart' => '2013-07-20',
            'rrpproxyContractEnd' => '2015-05-21',
            'rrpproxyContractRenewal' => '2015-05-20',
        ];
        $domain = Domain::createDomain($entry['domain']);
        $model = RrpproxyEntry::createOrUpdateRrpproxyEntry($entry, $domain);

        $this->assertModelExists($model);
        $this->assertEquals($model->id, RrpproxyEntry::first()->id);
        $this->assertEquals($model->domain->id, RrpproxyEntry::first()->domain->id);

        $newEntry = [
            'domain' => 'aks-service.de',
            'rrpproxyContractStart' => '2020-07-20',
            'rrpproxyContractEnd' => '2020-05-21',
            'rrpproxyContractRenewal' => '2020-05-20',
        ];
        $domain = Domain::createDomain($newEntry['domain']);
        $model = RrpproxyEntry::createOrUpdateRrpproxyEntry($newEntry, $domain);

        $this->assertModelExists($model);
        $this->assertEquals($model->id, RrpproxyEntry::first()->id);
    }
}
