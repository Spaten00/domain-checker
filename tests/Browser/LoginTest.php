<?php

namespace Tests\Browser;

use App\Models\Group;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Throwable;

class LoginTest extends DuskTestCase
{
    use DatabaseMigrations;

    /** @test */
    public function a_user_can_log_in()
    {
        Group::factory()->create();
        /** @var User $user */
        $user = User::factory()->create([
            'email' => 'tl@aks-service.de',
            'password' => password_hash('12345678', 1),
        ]);
        $this->browse(function (Browser $browser) use ($user) {
            $browser->visit('/login')
                ->type('email', $user->email)
                ->type('password', '12345678')
                ->press('login')
                ->assertPathIs('/');
        });
    }
}
