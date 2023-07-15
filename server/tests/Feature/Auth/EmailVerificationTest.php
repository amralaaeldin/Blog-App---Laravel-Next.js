<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
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

    public function test_email_can_be_verified(): void
    {
        Event::fake();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $this->user->id, 'hash' => sha1($this->user->email)]
        );

        $response = $this->actingAs($this->user)->get($verificationUrl);

        Event::assertDispatched(Verified::class);
        $this->assertTrue($this->user->fresh()->hasVerifiedEmail());
        $response->assertRedirect(config('app.frontend_url') . RouteServiceProvider::HOME . '?verified=1');
    }

    public function test_email_is_not_verified_with_invalid_hash(): void
    {
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $this->user->id, 'hash' => sha1('wrong-email')]
        );

        $this->actingAs($this->user)->get($verificationUrl);

        $this->assertFalse($this->user->fresh()->hasVerifiedEmail());
    }
}
