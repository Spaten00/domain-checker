<?php

namespace Tests\Browser\Pages;

use App\Models\Bill;
use App\Models\Contract;
use App\Models\Customer;
use App\Models\Domain;
use App\Models\RrpproxyEntry;
use App\Models\TanssEntry;
use Laravel\Dusk\Browser;
use Laravel\Dusk\Page;

class HomeWithData extends Page
{
    /**
     * Get the URL for the page.
     *
     * @return string
     */
    public function url()
    {
        return '/';
    }

    /**
     * Assert that the browser is on the page.
     *
     * @param Browser $browser
     * @return void
     */
    public function assert(Browser $browser)
    {
        $browser->assertPathIs($this->url());
    }

    /**
     * Get the element shortcuts for the page.
     *
     * @return array
     */
    public function elements()
    {
        return [
            '@element' => '#selector',
        ];
    }

    /**
     * Creates domains in the database for testing.
     *
     * @param $number
     */
    public function createDomains($number)
    {
        Domain::factory($number)->create();
    }

    /**
     * Creates a domain with TANSS and RRPproxy entries in the database for testing.
     */
    public function createDomainWithTanssAndRrpproxy()
    {
        Domain::factory()->create();
        Customer::factory()->create();
        TanssEntry::factory()->create();
        RrpproxyEntry::factory()->create();
    }

    /**
     * Creates a complete domain with entries and a contract but no bill in the database for testing.
     */
    public function createCompleteDomainWithoutBill()
    {
        /** @var Domain $domain */
        $domain = Domain::factory()->create();
        Customer::factory()->create();
        TanssEntry::factory()->create();
        RrpproxyEntry::factory()->create();
        $contract = Contract::factory()->create();
        $domain->contracts()->attach($contract);
    }

    /**
     * Creates a complete domain with entries and a contract and a bill in the database for testing.
     */
    public function createCompleteDomain()
    {
        /** @var Domain $domain */
        $domain = Domain::factory()->create();
        Customer::factory()->create();
        TanssEntry::factory()->create();
        RrpproxyEntry::factory()->create();
        $contract = Contract::factory()->create();
        Bill::factory()->create();
        $domain->contracts()->attach($contract);
    }
//        Customer::factory(10)->create();
//        Contract::factory(10)->hasDomains()->create();
//        Bill::factory(10)->create();
//        Domain::factory(10)->has(Contract::factory(5))->create();
//        Contract::factory(10)->has(Domain::factory())->create();
}
