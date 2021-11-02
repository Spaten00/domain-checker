<?php

namespace Tests\Browser;

use App\Models\Contract;
use App\Models\Customer;
use App\Models\Domain;
use App\Models\RrpproxyEntry;
use App\Models\TanssEntry;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\HomeWithData;
use Tests\DuskTestCase;

class ModalTest extends DuskTestCase
{
    use DatabaseMigrations;

    /** @test */
    public function the_modal_to_create_a_new_contract_gets_shown()
    {
        Domain::factory()->create();
        Customer::factory()->create();
        TanssEntry::factory()->create();
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->press('create1')
                ->waitForText('Speichern')
                ->assertSee('Noch kein');
        });
    }

    /** @test */
    public function the_modal_to_create_a_new_bill_gets_shown()
    {
        /** @var Domain $domain */
        $domain = Domain::factory()->create();
        Customer::factory()->create();
        TanssEntry::factory()->create();
        RrpproxyEntry::factory()->create();
        $contract = Contract::factory()->create();
        $domain->contracts()->attach($contract);
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->press('create1')
                ->waitForText('Speichern')
                ->assertSee('Neue Rechnungsnummer');
        });
    }

}
