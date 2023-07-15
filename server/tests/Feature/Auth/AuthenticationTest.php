<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = [
            'email' => 'testuser@test.com',
            'password' => '12345678',
        ];

        $createdUser = User::create(
            [
                'name' => 'testuser',
                'email' => 'testuser@test.com',
                'password' => bcrypt('12345678'),
            ]
        );

        $createdUser->assignRole('user');
    }

    public function test_users_can_login(): void
    {
        $response = $this->post('api/login', $this->user);

        $response->assertStatus(200);
        $this->assertArrayHasKey('token', $response->json());
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $response = $this->post('api/login', [
            'email' => $this->user['email'],
            'password' => 'wrong-password',
        ]);

        $response->assertSee(__('The provided credentials are incorrect.'));
    }

    public function test_validation_works_correctly_when_login()
    {
        $response = $this->post('api/login', [
            'email' => $this->user['email'],
        ]);

        $response->assertJsonStructure([
            'errors' => [
                'password',
            ],
        ]);
        $response->assertStatus(400);
    }
}
