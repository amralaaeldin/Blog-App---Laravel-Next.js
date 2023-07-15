<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = [
            'name' => 'testuser',
            'email' => 'testuser@test.com',
            'password' => '12345678',
            'password_confirmation' => '12345678',
        ];
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post('api/register', $this->user);

        $response->assertStatus(201);
        $this->assertDatabaseHas('users', [
            "name" => $this->user['name'],
            "email" => $this->user['email'],
        ]);
        $this->assertNull(User::find($response->json('user')['id'])->accepted_at);
    }

    public function test_validation_works_correctly_when_registeration()
    {
        $response = $this->post('api/register', [
            'email' => $this->user['email'],
            'password' => '12345678',
            'password_confirmation' => '12345678',
        ]);

        $response->assertJsonStructure([
            'errors' => [
                'name',
            ],
        ]);
        $response->assertStatus(400);
    }
}
