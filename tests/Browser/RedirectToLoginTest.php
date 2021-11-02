<?php

namespace Tests\Browser;

use App\Models\Customer;
use App\Models\Domain;
use App\Models\TanssEntry;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class RedirectToLoginTest extends DuskTestCase
{
    use DatabaseMigrations;

    /** @test */
    public function the_user_gets_redirected_to_the_login_page_if_he_tries_to_create_a_contract()
    {
        Domain::factory()->create();
        Customer::factory()->create();
        TanssEntry::factory()->create();
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->press('create1')
                ->waitForText('Speichern')
                ->press('save')
                ->assertPathIs('/login');
        });
    }

    /** @test */
    public function the_user_gets_redirected_to_the_login_page_if_he_tries_to_create_a_bill()
    {
        Domain::factory()->create();
        Customer::factory()->create();
        TanssEntry::factory()->create();
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->press('create1')
                ->waitForText('Speichern')
                ->press('save')
                ->assertPathIs('/login');
        });
    }

    /** @test */
    public function the_user_gets_redirected_to_the_login_page_if_he_tries_to_update_a_contract_or_a_bill()
    {
        Domain::factory()->create();
        Customer::factory()->create();
        TanssEntry::factory()->create();
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->click('tr td a')
                ->assertPathIs('/login');
        });
    }
}
