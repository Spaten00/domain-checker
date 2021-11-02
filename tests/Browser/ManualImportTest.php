<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ManualImportTest extends DuskTestCase
{
    /** @test */
    public function the_user_can_start_the_manual_import()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->clickLink('Daten-Import manuell starten')
                ->type('searchString', 'aks')
                ->press('searchButton')
                ->assertSee('aks');
        });
    }
}
