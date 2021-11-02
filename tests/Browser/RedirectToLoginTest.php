<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\HomeWithData;
use Tests\DuskTestCase;

class RedirectToLoginTest extends DuskTestCase
{
    use DatabaseMigrations;


    // TODO move and modal for bill

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
    public function the_user_gets_redirected_to_the_login_page_if_he_tries_to_create_a_contract()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(new HomeWithData())
                ->createDomains()
                ->press('create1')
                ->waitForText('Speichern')
                ->press('save')
                ->assertPathIs('/login');
        });
    }

    /** @test */
    public function the_user_gets_redirected_to_the_login_page_if_he_tries_to_create_a_bill()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(new HomeWithData())
                ->createDomains()
                ->press('create1')
                ->waitForText('Speichern')
                ->press('save')
                ->assertPathIs('/login');
        });
    }

    /** @test */
    public function the_user_gets_redirected_to_the_login_page_if_he_tries_to_update_a_contract_or_a_bill()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(new HomeWithData())
                ->createDomains()
                ->click('tr td a')
                ->assertPathIs('/login');
        });
    }
}
