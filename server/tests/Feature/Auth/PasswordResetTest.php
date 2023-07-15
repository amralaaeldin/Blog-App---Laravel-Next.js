<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::create(
            [
                'name' => 'testuser',
                'email' => 'testuser@test.com',
                'password' => bcrypt('12345678'),
            ]
        );

        $this->user->assignRole('user');
    }

    public function test_reset_password_link_can_be_requested(): void
    {
        Notification::fake();

        $this->post('api/forgot-password', ['email' => $this->user->email]);

        Notification::assertSentTo($this->user, ResetPassword::class);
    }

    public function test_password_can_be_reset_with_valid_token(): void
    {
        Notification::fake();

        $this->post('api/forgot-password', ['email' => $this->user->email]);

        Notification::assertSentTo($this->user, ResetPassword::class, function (object $notification) {
            $response = $this->post('api/reset-password', [
                'token' => $notification->token,
                'email' => $this->user->email,
                'password' => 'password',
                'password_confirmation' => 'password',
            ]);

            $response->assertSessionHasNoErrors();

            return true;
        });
    }
}
