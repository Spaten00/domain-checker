<?php

namespace Tests\Feature;

use App\Models\Group;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_view_a_login_form()
    {
        $response = $this->get('/login');

        $response->assertSuccessful();
        $response->assertViewIs('auth.login');
    }

    /** @test */
    public function user_cannot_view_a_login_form_when_authenticated()
    {
        /** @var Group $group */
        $group = Group::factory()->create();
        /** @var User $user */
        $user = User::factory()->create(['group_id' => $group->id]);

        $response = $this->actingAs($user)->get('/login');

        $response->assertRedirect('/dashboard');
    }

    /** @test */
    public function an_unverified_user_can_login()
    {
        /** @var Group $group */
        $group = Group::factory()->create();
        /** @var User $user */
        $user = User::factory()->unverified()->create(['group_id' => $group->id]);

        $response = $this->actingAs($user)->get('/login');

        $response->assertRedirect('/dashboard');
    }

    /** @test */
    public function a_registered_user_can_login_with_correct_credentials()
    {
        /** @var Group $group */
        $group = Group::factory()->create();
        /** @var User $user */
        $user = User::factory()->create([
            'group_id' => $group->id,
            'password' => bcrypt($password = 'supersecretpassword'),
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => $password,
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function a_registered_user_cannot_login_with_incorrect_password()
    {
        /** @var Group $group */
        $group = Group::factory()->create();
        /** @var User $user */
        $user = User::factory()->create([
            'group_id' => $group->id,
            'password' => bcrypt($password = 'supersecretpassword'),
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'notthecorrectpassword',
        ]);

        $response->assertRedirect('/');
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /** @test */
    public function remember_me_functionality()
    {
        /** @var Group $group */
        $group = Group::factory()->create();
        /** @var User $user */
        $user = User::factory()->create([
            'group_id' => $group->id,
            'id' => rand(1, 100),
            'password' => bcrypt($password = 'supersecretpassword'),
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => $password,
            'remember' => 'on',
        ]);

        $response->assertRedirect('/dashboard');
        $response->assertCookie(Auth::guard()->getRecallerName(), vsprintf('%s|%s|%s', [
            $user->id,
            $user->getRememberToken(),
            $user->password,
        ]));
        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function an_unregistered_user_cannot_login()
    {
        $response = $this->post('/login', [
            'email' => 'someemail',
            'password' => 'somepassword',
        ]);

        $response->assertRedirect('/');
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }
}
