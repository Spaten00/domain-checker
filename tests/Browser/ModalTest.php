<?php

namespace Tests\Browser;

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
        $this->browse(function (Browser $browser) {
            $browser->visit(new HomeWithData())
                ->createDomains()
                ->press('create1')
                ->waitForText('Speichern')
                ->assertSee('Noch kein');
        });
    }

    /** @test */
    public function the_modal_to_create_a_new_bill_gets_shown()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(new HomeWithData())
                ->createCompleteDomainWithoutBill()
                ->press('create1')
                ->waitForText('Speichern')
                ->assertSee('Neue Rechnungsnummer');
        });
    }

}
