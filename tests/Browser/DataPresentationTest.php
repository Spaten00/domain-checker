<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\HomeWithData;
use Tests\DuskTestCase;

class DataPresentationTest extends DuskTestCase
{
    use DatabaseMigrations;

    /** @test */
    public function the_status_of_missing_entries_is_shown()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(new HomeWithData())
                ->createDomains()
                ->assertSee('Keine Einträge')
                ->assertSee('fehlt');
        });
    }

    /** @test */
    public function the_status_of_an_ok_entry_is_shown()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(new HomeWithData())
                ->createACompleteEntry()
                ->assertSee('OK');
        });
    }
}
