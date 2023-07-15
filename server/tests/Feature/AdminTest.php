<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Tests\TestHelpers;
use Tests\TestCase;

class AdminTest extends TestCase
{
  use RefreshDatabase, TestHelpers;

  private User $user;
  private User $admin;
  private User $superAdmin;
  private $adminData;

  protected function setUp(): void
  {
    parent::setUp();
    $this->user = $this->createUserWithRole('user');
    $this->admin = $this->createUserWithRole('admin');
    $this->superAdmin = $this->createUserWithRole('super_admin');

    $this->adminData = [
      'name' => "Some Name",
      'email' => fake()->unique()->safeEmail(),
      'password' => "12345678",
      'password_confirmation' => "12345678",
    ];
  }

  public function test_super_admin_can_get_all_admins()
  {
    $response = $this->actingAs($this->superAdmin)->get('/api/admins');

    $response->assertStatus(200);
    $response->assertSee($this->admin->name);
    $response->assertJsonStructure([
      '*' => [
        'id',
        'name',
        'email',
      ],
    ]);
  }

  public function test_not_authorized_if_not_super_admin()
  {
    $responseForAdmin = $this->actingAs($this->admin)->get('/api/admins');
    $responseForUser = $this->actingAs($this->user)->get('/api/admins');
    $responseForNotAuth = $this->get('/api/admins');

    $responseForAdmin->assertStatus(403);
    $responseForUser->assertStatus(403);
    $responseForNotAuth->assertStatus(403);
  }

  public function test_super_admin_can_get_an_admin()
  {
    $response = $this->actingAs($this->superAdmin)->get('/api/admins/' . $this->admin->id);

    $response->assertStatus(200);
    $response->assertSee($this->admin->name);
    $response->assertJsonStructure([
      'id',
      'name',
      'email',
    ]);
  }

  public function test_gets_not_found_if_no_admin_found_with_id()
  {
    $responseForUserId = $this->actingAs($this->superAdmin)->get('/api/admins/' . $this->user->id);
    $responseForNotExists = $this->actingAs($this->superAdmin)->get('/api/admins/100');

    $responseForUserId->assertStatus(404);
    $responseForNotExists->assertStatus(404);
    $responseForUserId->assertJsonStructure([
      'message',
    ]);
    $responseForNotExists->assertJsonStructure([
      'message',
    ]);
  }

  public function test_super_admin_can_create_an_admin()
  {
    $response = $this->actingAs($this->superAdmin)->post('/api/admins', $this->adminData);

    $response->assertStatus(201);
    $response->assertSee($this->adminData['name']);
    $response->assertJsonStructure([
      'admin' => [
        'id',
        'name',
        'email',
      ],
      'message',
    ]);
    $this->assertDatabaseHas('users', [
      'name' => $this->adminData['name'],
      'email' => $this->adminData['email'],
    ]);
    $this->assertTrue(User::find($response['admin']['id'])->hasRole('admin'));
  }

  public function test_validation_works_correctly_when_creating_an_admin()
  {
    $response = $this->actingAs($this->superAdmin)->post('/api/admins', [
      'email' => fake()->unique()->safeEmail(),
      'password' => "12345678",
      'password_confirmation' => "12345678",
    ]);

    $response->assertJsonStructure([
      'errors' => [
        'name',
      ],
    ]);
    $response->assertStatus(400);
  }

  public function test_super_admin_can_update_an_admin()
  {
    $createdAdmin = $this->createUserWithRole('admin');

    $response = $this->actingAs($this->superAdmin)->patch('/api/admins/' . $createdAdmin->id, $this->adminData);

    $response->assertStatus(200);
    $this->assertDatabaseHas('users', [
      'id' => $createdAdmin->id,
      'name' => $this->adminData['name'],
    ]);
  }

  public function test_not_found_if_not_admin_id()
  {
    $response = $this->actingAs($this->superAdmin)->patch('/api/admins/' . $this->user->id, $this->adminData);

    $response->assertStatus(404);
  }

  public function test_super_admin_can_delete_an_admin()
  {
    $response = $this->actingAs($this->superAdmin)->delete('/api/admins/' . $this->admin->id);

    $response->assertStatus(200);
    $this->assertDatabaseMissing('users', [
      'id' => $this->admin->id,
    ]);
  }
}
