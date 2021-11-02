<?php

namespace Tests\Browser;

use App\Models\Customer;
use App\Models\Domain;
use App\Models\RrpproxyEntry;
use App\Models\TanssEntry;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class DataPresentationTest extends DuskTestCase
{
    use DatabaseMigrations;

    /** @test */
    public function the_status_of_missing_entries_is_shown()
    {
        Domain::factory()->create();
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->assertSee('Keine Einträge')
                ->assertSee('fehlt');
        });
    }

    /** @test */
    public function the_status_of_an_ok_entry_is_shown()
    {
        Domain::factory()->create();
        Customer::factory()->create();
        TanssEntry::factory()->create();
        RrpproxyEntry::factory()->create();
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->assertSee('OK');
        });
    }

    /** @test */
    public function the_user_can_use_the_search_bar()
    {
        Domain::factory(50)->create(['name' => 'a-test']);
        Domain::factory()->create(['name' => 'b-test']);
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->assertSee('a-test')
                ->assertDontSee('b-test')
                ->type('searchString', 'b-test')
                ->press('searchButton')
                ->assertSee('b-test');
        });
    }

    /** @test */
    public function the_user_can_visit_the_all_domains_page()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->clickLink('Alle Domains')
                ->assertPathIs('/');
        });
    }

    /** @test */
    public function the_user_can_visit_the_active_domains_page()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->clickLink('Aktive Domains')
                ->assertPathIs('/active');
        });
    }

    /** @test */
    public function the_user_can_visit_the_incomplete_page()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->clickLink('Unvollständige/Fehlerhafte Daten')
                ->assertPathIs('/incomplete');
        });
    }

    /** @test */
    public function the_user_can_visit_the_expiring_domains_page()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->clickLink('Auslaufende Verträge')
                ->assertPathIs('/expiring');
        });
    }

    /** @test */
    public function the_user_can_sort_by_domains()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->click('#domain')
                ->assertPathIs('/sort/domains.name/asc')
                ->click('#domain')
                ->assertPathIs('/sort/domains.name/desc');
        });
    }

    /** @test */
    public function the_user_can_sort_by_customers()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->click('#customer')
                ->assertPathIs('/sort/customers.name/asc')
                ->click('#customer')
                ->assertPathIs('/sort/customers.name/desc');
        });
    }

    /** @test */
    public function the_user_can_sort_by_tanss_contract_end()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->click('#tanss-contract-end')
                ->assertPathIs('/sort/tanss_entries.contract_end/asc')
                ->click('#tanss-contract-end')
                ->assertPathIs('/sort/tanss_entries.contract_end/desc');
        });
    }

    /** @test */
    public function the_user_can_sort_by_rrpproxy_contract_end()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->click('#rrpproxy-contract-end')
                ->assertPathIs('/sort/rrpproxy_entries.contract_end/asc')
                ->click('#rrpproxy-contract-end')
                ->assertPathIs('/sort/rrpproxy_entries.contract_end/desc');
        });
    }

    /** @test */
    public function the_user_can_sort_by_rrpproxy_contract_renewal()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->click('#rrpproxy-contract-renewal')
                ->assertPathIs('/sort/rrpproxy_entries.contract_renewal/asc')
                ->click('#rrpproxy-contract-renewal')
                ->assertPathIs('/sort/rrpproxy_entries.contract_renewal/desc');
        });
    }

    /** @test */
    public function the_user_can_sort_by_contract_number()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->click('#contract-number')
                ->assertPathIs('/sort/contracts.contract_number/asc')
                ->click('#contract-number')
                ->assertPathIs('/sort/contracts.contract_number/desc');
        });
    }

    /** @test */
    public function the_user_can_sort_by_bill_number()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->click('#bill-number')
                ->assertPathIs('/sort/bills.bill_number/asc')
                ->click('#bill-number')
                ->assertPathIs('/sort/bills.bill_number/desc');
        });
    }

    /** @test */
    public function the_user_can_sort_by_last_bill_date()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->click('#last-bill-date')
                ->assertPathIs('/sort/bills.date/asc')
                ->click('#last-bill-date')
                ->assertPathIs('/sort/bills.date/desc');
        });
    }
}
