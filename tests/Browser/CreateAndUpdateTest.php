<?php

namespace Tests\Browser;

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
        $this->browse(function (Browser $browser) {
            $browser->visit(new Login())
                ->loginUser()
                ->visit(new HomeWithData())
                ->createDomains()
                ->press('create1')
                ->waitForText('Speichern')
                ->press('save')
                ->assertSee('The contract number field is required.');
        });
    }

    /** @test */
    public function the_user_can_create_a_new_contract()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(new Login())
                ->loginUser()
                ->visit(new HomeWithData())
                ->createDomains()
                ->press('create1')
                ->waitForText('Speichern')
                ->type('contract_number','42')
                ->press('save')
                ->assertSee('Eintrag wurde erstellt.');
        });
    }
}
