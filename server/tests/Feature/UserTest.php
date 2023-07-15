<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestHelpers;
use App\Models\User;
use Tests\TestCase;

class UserTest extends TestCase
{
  use RefreshDatabase, TestHelpers;

  private User $user;
  private User $admin;
  private User $superAdmin;

  protected function setUp(): void
  {
    parent::setUp();
    $this->user = $this->createUserWithRole('user');
    $this->admin = $this->createUserWithRole('admin');
    $this->superAdmin = $this->createUserWithRole('super_admin');
  }

  public function test_super_admin_or_admin_can_get_all_users()
  {
    $user = $this->createUserWithRole('user');
    $user->accepted_at = date("Y-m-d H:i:s");
    $user->save();

    $responseForSuperAdmin = $this->actingAs($this->superAdmin)->get('/api/users');
    $responseForAdmin = $this->actingAs($this->admin)->get('/api/users');

    $responseForSuperAdmin->assertStatus(200);
    $responseForSuperAdmin->assertSee($user->name);
    $responseForSuperAdmin->assertJsonStructure([
      '*' => [
        'id',
        'name',
        'email',
      ],
    ]);
    $responseForAdmin->assertStatus(200);
    $responseForAdmin->assertSee($user->name);
    $responseForAdmin->assertJsonStructure([
      '*' => [
        'id',
        'name',
        'email',
      ],
    ]);
  }

  public function test_not_authorized_if_not_super_admin_nor_admin()
  {
    $responseForUser = $this->actingAs($this->user)->get('/api/users');
    $responseForNotAuth = $this->get('/api/users');

    $responseForUser->assertStatus(403);
    $responseForNotAuth->assertStatus(403);
  }

  public function test_super_admin_or_admin_can_get_a_user()
  {
    $response = $this->actingAs($this->superAdmin)->get('/api/users/' . $this->user->id);

    $response->assertStatus(200);
    $response->assertSee($this->user->name);
    $response->assertJsonStructure([
      'id',
      'name',
      'email',
    ]);
  }

  public function test_not_found_if_not_exists()
  {
    $response = $this->actingAs($this->superAdmin)->get('/api/users/1000');

    $response->assertStatus(404);
  }

  public function test_super_admin_or_admin_can_get_pending_users()
  {
    $response = $this->actingAs($this->superAdmin)->get('/api/users/pending');

    $response->assertStatus(200);
    $response->assertSee($this->user->name);
    $response->assertJsonStructure([
      '*' => [
        'id',
        'name',
        'email',
        'created_at',
      ],
    ]);
  }

  public function test_super_admin_or_admin_can_accept_a_user()
  {
    $response = $this->actingAs($this->superAdmin)->patch('/api/users/' . $this->user->id . '/accept');

    $response->assertStatus(200);
    $response->assertJsonStructure([
      'message'
    ]);
    $this->assertNotNull(User::find($this->user->id)->accepted_at);
  }

  public function test_not_found_if_not_a_user_id()
  {
    $response = $this->actingAs($this->superAdmin)->get('/api/users/1');

    $response->assertStatus(404);
  }

  public function test_super_admin_or_admin_can_delete_a_user()
  {
    $response = $this->actingAs($this->superAdmin)->delete('/api/users/' . $this->user->id);

    $response->assertStatus(200);
    $response->assertJsonStructure([
      'message',
    ]);
    $this->assertDatabaseMissing('users', [
      'id' => $this->user->id,
    ]);
  }

  public function test_user_can_get_his_profile()
  {
    $response = $this->actingAs($this->user)->get('/api/u/profile');

    $response->assertStatus(200);
    $response->assertSee($this->user->name);
    $response->assertJsonStructure([
      'id',
      'name',
      'email',
    ]);
  }

  public function  test_user_can_update_his_profile()
  {
    $response = $this->actingAs($this->user)->patch('/api/u/profile', [
      'name' => 'new name',
    ]);

    $response->assertStatus(200);
    $this->assertDatabaseHas('users', [
      'id' => $this->user->id,
      'name' => 'new name',
    ]);
  }

  public function test_validation_works_correctly_when_updating_a_user()
  {
    $response = $this->actingAs($this->user)->patch('/api/u/profile', [
      'name' => 12345,
    ]);

    $response->assertJsonStructure([
      'errors' => [
        'name',
      ],
    ]);
    $response->assertStatus(400);
  }

  public function test_not_authorized_if_not_user()
  {
    $responseForAdmin = $this->actingAs($this->admin)->get('/api/u/profile');
    $responseForSuperAdmin = $this->actingAs($this->superAdmin)->get('/api/u/profile');
    $responseForNotAuth = $this->get('/api/u/profile');

    $responseForAdmin->assertStatus(403);
    $responseForSuperAdmin->assertStatus(403);
    $responseForNotAuth->assertStatus(403);
  }
}
