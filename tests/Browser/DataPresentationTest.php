<?php

namespace Tests\Browser;

use App\Models\Customer;
use App\Models\Domain;
use App\Models\RrpproxyEntry;
use App\Models\TanssEntry;
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
}
