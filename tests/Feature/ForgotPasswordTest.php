<?php

namespace Tests\Feature;

use Exception;
use App\Models\Group;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ForgotPasswordTest extends TestCase
{
    use RefreshDatabase;

    /** @test
     * @throws Exception
     */
    public function user_receives_an_email_with_a_password_reset_link()
    {
        Notification::fake();

        /** @var Group $group */
        $group = Group::factory()->create();
        /** @var User $user */
        $user = User::factory()->create(['group_id' => $group->id]);

        $response = $this->post('/password/email', [
            'email' => $user->email,
        ]);

        $token = DB::table('password_resets')->first();
        $this->assertNotNull($token);

        Notification::assertSentTo($user, ResetPassword::class, function ($notification, $channels) use ($token) {
            return Hash::check($notification->token, $token->token) === true;
        });
    }
}
