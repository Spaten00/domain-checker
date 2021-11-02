<?php

namespace Tests\Browser;

use App\Models\Bill;
use App\Models\Contract;
use App\Models\Customer;
use App\Models\Domain;
use App\Models\RrpproxyEntry;
use App\Models\TanssEntry;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\HomeWithData;
use Tests\Browser\Pages\Login;
use Tests\DuskTestCase;

class CreateAndUpdateTest extends DuskTestCase
{
    use DatabaseMigrations;

    /** @test */
    public function the_user_gets_a_message_when_he_tries_to_create_a_new_contract_without_a_number()
    {
        Domain::factory()->create();
        Customer::factory()->create();
        TanssEntry::factory()->create();
        $this->browse(function (Browser $browser) {
            $browser->visit(new Login())
                ->loginUser()
                ->press('create1')
                ->waitForText('Speichern')
                ->press('save')
                ->assertSee('The contract number field is required.');
        });
    }

    /** @test */
    public function the_user_can_create_a_new_contract()
    {
        Domain::factory()->create();
        Customer::factory()->create();
        TanssEntry::factory()->create();
        $this->browse(function (Browser $browser) {
            $browser->visit(new Login())
                ->loginUser()
                ->press('create1')
                ->waitForText('Speichern')
                ->type('contract_number', '42235')
                ->press('save')
                ->assertSee('Eintrag wurde erstellt!');
        });
    }

    /** @test */
    public function the_user_can_create_new_bills()
    {
        /** @var Domain $domain */
        $domain = Domain::factory()->create();
        Customer::factory()->create();
        TanssEntry::factory()->create();
        RrpproxyEntry::factory()->create();
        $contract = Contract::factory()->create();
        $domain->contracts()->attach($contract);
        $this->browse(function (Browser $browser) {
            $browser->visit(new Login())
                ->loginUser()
                ->press('create1')
                ->waitForText('Speichern')
                ->type('bill_number', '42235')
                ->press('save')
                ->assertSee('Eintrag wurde erstellt!')
                ->press('create1')
                ->waitForText('Speichern')
                ->type('bill_number', '12345')
                ->press('save')
                ->assertSee('Eintrag wurde erstellt!');
        });
    }

    /** @test */
    public function the_user_can_update_the_contract_number()
    {
        /** @var Domain $domain */
        $domain = Domain::factory()->create();
        Customer::factory()->create();
        TanssEntry::factory()->create();
        RrpproxyEntry::factory()->create();
        $contract = Contract::factory()->create();
        $domain->contracts()->attach($contract);
        $this->browse(function (Browser $browser) {
            $browser->visit(new Login())
                ->loginUser()
                ->click('tr td i')
                ->type('#contract-number-1', '42')
                ->click('tr td i')
                ->waitForDialog()
                ->acceptDialog()
                ->assertInputValue('#contract-number-1', '42');
        });
    }

    /** @test */
    public function the_user_can_update_the_bill_number()
    {
        /** @var Domain $domain */
        $domain = Domain::factory()->create();
        Customer::factory()->create();
        TanssEntry::factory()->create();
        RrpproxyEntry::factory()->create();
        $contract = Contract::factory()->create();
        Bill::factory()->create();
        $domain->contracts()->attach($contract);
        $this->browse(function (Browser $browser) {
            $browser->visit(new Login())
                ->loginUser()
                ->click('tr td i')
                ->type('#bill-number-1', '42')
                ->click('tr td i')
                ->waitForDialog()
                ->acceptDialog()
                ->assertInputValue('#bill-number-1', '42');
        });
    }
}
