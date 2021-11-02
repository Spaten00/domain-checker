<?php

namespace Tests\Browser\Pages;

use App\Models\Group;
use App\Models\User;
use Laravel\Dusk\Browser;
use Laravel\Dusk\Page;

class Login extends Page
{
    /**
     * Get the URL for the page.
     *
     * @return string
     */
    public function url()
    {
        return '/login';
    }

    /**
     * Assert that the browser is on the page.
     *
     * @param Browser $browser
     * @return void
     */
    public function assert(Browser $browser)
    {
        $browser->assertPathIs($this->url());
    }

    /**
     * Get the element shortcuts for the page.
     *
     * @return array
     */
    public function elements()
    {
        return [
            '@element' => '#selector',
        ];
    }

    /**
     * Login as a user.
     *
     * @param Browser $browser
     */
    public function loginUser(Browser $browser)
    {
        Group::factory()->create();
        /** @var User $user */
        $user = User::factory()->create([
            'email' => 'tl@aks-service.de',
            'password' => password_hash('12345677', 1),
        ]);
        $browser->visit('/login')
            ->type('email', $user->email)
            ->type('password', '12345677')
            ->press('login');

    }

}
