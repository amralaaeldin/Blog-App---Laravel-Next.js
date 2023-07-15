<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Tests\TestHelpers;
use Tests\TestCase;

class PostTest extends TestCase
{
  use RefreshDatabase;
  use TestHelpers;

  private User $user;
  private User $superAdmin;
  private $post;

  protected function setUp(): void
  {
    parent::setUp();
    $this->user = $this->createUserWithRole('user');
    $this->superAdmin = $this->createUserWithRole('super_admin');
    $this->post = [
      'title' => fake()->sentence,
      'body' => fake()->paragraph,
      'user_id' => $this->user->id,
      'tagNames' => ['tag1', 'tag2'],
    ];
  }

  public function test_user_can_get_all_posts()
  {
    $response = $this->actingAs($this->user)->get('/api/posts');

    $response->assertStatus(200);
    $response->assertJsonStructure([
      '*' => [
        'id',
        'title',
        'body',
        'created_at',
        'updated_at',
        'user_id',
        'comments_count',
        'user' => [
          'id',
          'name',
          'email',
        ],
      ],
    ]);
  }

  public function test_not_authorized_if_not_logged_in()
  {
    $response = $this->getJson('/api/posts');

    $this->assertContains($response->status(), [401, 403]);
  }

  public function test_user_can_get_a_post()
  {
    $post = $this->createPost($this->user, $this->post);

    $response = $this->actingAs($this->user)->get('/api/posts/' . $post->id);

    $response->assertStatus(200);
    $response->assertJsonStructure([
      'id',
      'title',
      'body',
      'created_at',
      'updated_at',
      'user_id',
      'comments_count',
      'comments' => [
        '*' => [
          'id',
          'body',
          'user' => [
            'id',
            'name',
            'email',
          ],
        ],
      ],
      'user' => [
        'id',
        'name',
        'email',
      ],
    ]);
  }

  public function test_user_gets_not_found_error_when_getting_a_post_with_invalid_id()
  {
    $response = $this->actingAs($this->user)->get('/api/posts/1000');

    $this->assertContains($response->status(), [404, 403]);
    $response->assertJsonStructure([
      'message',
    ]);
  }

  public function test_user_can_get_a_post_with_comments()
  {
    $post = $this->createPost($this->user, $this->post);

    $response = $this->actingAs($this->user)->get('/api/posts/' . $post->id . '/comments');

    $response->assertStatus(200);
    $response->assertJsonStructure([
      'id',
      'title',
      'body',
      'created_at',
      'updated_at',
      'user_id',
      'comments_count',
      'comments' => [
        '*' => [
          'id',
          'body',
          'user' => [
            'id',
            'name',
            'email',
          ],
        ],
      ],
      'user' => [
        'id',
        'name',
        'email',
      ],
    ]);
  }

  public function test_not_accepted_users_cannot_post()
  {
    $response = $this->actingAs($this->user)->post('/api/posts', $this->post);

    $response->assertStatus(403);
    $response->assertJsonStructure([
      'message',
    ]);
  }

  public function test_accepted_users_can_create_a_post()
  {
    $this->user->update([
      'accepted_at' => date("Y-m-d H:i:s"),
    ]);
    $response = $this->actingAs($this->user)->post('/api/posts', $this->post);

    $response->assertStatus(201);
    $response->assertJsonStructure([
      'id',
      'title',
      'body',
      'created_at',
      'updated_at',
    ]);
    foreach ($this->post['tagNames'] as $tagName) {
      $this->assertDatabaseHas('tags', [
        'name' => $tagName,
      ]);
    }
  }

  public function test_validation_on_post_creation_works_correctly()
  {
    $this->user->update([
      'accepted_at' => date("Y-m-d H:i:s"),
    ]);

    $response = $this->actingAs($this->user)->post('/api/posts', [
      'title' => '',
    ]);

    $response->assertStatus(400);
    $response->assertJsonStructure([
      'errors' => [
        'title',
        'body',
      ],
    ]);
  }

  public function test_user_can_update_his_post()
  {
    $post = $this->createPost($this->user, $this->post);

    $response = $this->actingAs($this->user)->patch('/api/posts/' . $post->id, [
      'title' => 'updated title',
      'body' => 'updated body',
      'tagNames' => ['tag3', 'tag4'],
    ]);

    $response->assertStatus(200);
    $response->assertJsonStructure([
      'message'
    ]);
    $this->assertDatabaseHas('posts', [
      'title' => 'updated title',
      'body' => 'updated body',
    ]);
    foreach (['tag3', 'tag4'] as $tagName) {
      $this->assertDatabaseHas('tags', [
        'name' => $tagName,
      ]);
    }
  }

  public function test_validation_on_post_update_works_correctly()
  {
    $post = $this->createPost($this->user, $this->post);

    $response = $this->actingAs($this->user)->patch('/api/posts/' . $post->id, [
      'title' => '',
    ]);

    $response->assertStatus(400);
    $response->assertJsonStructure([
      'errors' => [
        'title',
        'body',
      ],
    ]);
  }

  public function test_user_gets_not_found_error_when_updating_a_post_with_invalid_id()
  {
    $response = $this->actingAs($this->user)->patch('/api/posts/1000');

    $this->assertContains($response->status(), [404, 403]);
    $response->assertJsonStructure([
      'message',
    ]);
  }

  public function test_user_gets_not_found_error_when_deleting_a_post_with_invalid_id()
  {
    $response = $this->actingAs($this->user)->delete('/api/posts/1000');

    $this->assertContains($response->status(), [404, 403]);
    $response->assertJsonStructure([
      'message',
    ]);
  }

  public function test_user_gets_unauthorized_error_when_updating_a_post_doesnot_belong_to_him()
  {
    $post = $this->createPost($this->user, $this->post);
    $newUser = $this->createUserWithRole('user');

    $response = $this->actingAs($newUser)->patch('/api/posts/' . $post->id, [
      'title' => 'updated title',
      'body' => 'updated body',
      'tagNames' => ['tag3', 'tag4'],
    ]);

    $response->assertStatus(403);
    $response->assertJsonStructure([
      'message',
    ]);
  }

  public function test_user_can_delete_his_post()
  {
    $post = $this->createPost($this->user, $this->post);

    $response = $this->actingAs($this->user)->delete('/api/posts/' . $post->id);

    $response->assertStatus(200);
    $response->assertJsonStructure([
      'message',
    ]);
    $this->assertDatabaseMissing('posts', [
      'id' => $post->id,
    ]);
  }

  public function test_user_gets_unauthorized_error_when_deleting_a_post_doesnot_belong_to_him()
  {
    $post = $this->createPost($this->user, $this->post);
    $newUser = $this->createUserWithRole('user');

    $response = $this->actingAs($newUser)->delete('/api/posts/' . $post->id);

    $response->assertStatus(403);
    $response->assertJsonStructure([
      'message',
    ]);
  }

  public function test_super_admin_and_admin_can_delete_any_post()
  {
    $post = $this->createPost($this->user, $this->post);

    $response = $this->actingAs($this->superAdmin)->delete('/api/posts/' . $post->id);

    $response->assertStatus(200);
    $response->assertJsonStructure([
      'message',
    ]);
    $this->assertDatabaseMissing('posts', [
      'id' => $post->id,
    ]);
  }
}
