<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Tests\TestHelpers;
use Tests\TestCase;

class CommentTest extends TestCase
{
  use RefreshDatabase;
  use TestHelpers;

  private User $user;
  private User $anotherUser;
  private User $superAdmin;
  private $post;
  private $comment;

  protected function setUp(): void
  {
    parent::setUp();
    $this->user = $this->createUserWithRole('user');
    $this->anotherUser = $this->createUserWithRole('user');
    $this->superAdmin = $this->createUserWithRole('super_admin');
    $this->post = [
      'title' => fake()->sentence,
      'body' => fake()->paragraph,
      'user_id' => $this->user->id,
      'tagNames' => ['tag1', 'tag2'],
    ];
    $this->comment = [
      'body' => fake()->paragraph,
      'user_id' => $this->anotherUser->id,
    ];
  }

  public function test_user_can_create_a_comment()
  {
    $this->anotherUser->update([
      'accepted_at' => date("Y-m-d H:i:s"),
    ]);
    $post = $this->createPost($this->user, $this->post);

    $this->actingAs($this->anotherUser)->post('/api/posts/' . $post->id . '/comments', $this->comment);

    $this->assertDatabaseHas('comments', [
      'body' => $this->comment['body'],
      'user_id' => $this->anotherUser->id,
      'post_id' => $post->id,
    ]);
  }

  public function test_validation_works_correctly_when_creating_a_comment()
  {
    $this->anotherUser->update([
      'accepted_at' => date("Y-m-d H:i:s"),
    ]);
    $post = $this->createPost($this->user, $this->post);

    $response = $this->actingAs($this->anotherUser)->post('/api/posts/' . $post->id . '/comments', [
      'body' => '',
    ]);

    $response->assertStatus(400);
    $response->assertJsonStructure([
      'errors' => [
        'body',
      ],
    ]);
  }

  public function test_gets_not_found_if_post_invalid_id()
  {
    $this->user->update([
      'accepted_at' => date("Y-m-d H:i:s"),
    ]);
    $response = $this->actingAs($this->user)->post('/api/posts/1000/comments', $this->comment);

    $response->assertStatus(404);
    $response->assertJsonStructure([
      'message',
    ]);
  }

  public function test_not_accepted_users_cannot_comment()
  {
    $post = $this->createPost($this->user, $this->post);

    $response = $this->actingAs($this->anotherUser)->post('/api/posts/' . $post->id . '/comments', $this->comment);

    $response->assertStatus(403);
    $response->assertJsonStructure([
      'message',
    ]);
  }

  public function test_user_can_update_a_comment()
  {
    $this->anotherUser->update([
      'accepted_at' => date("Y-m-d H:i:s"),
    ]);
    $post = $this->createPost($this->user, $this->post);
    $comment = $this->createComment($this->anotherUser, $post, $this->comment);

    $response = $this->actingAs($this->anotherUser)->patch('/api/posts/' . $post->id . '/comments/' . $comment->id, [
      'body' => 'updated body',
    ]);

    $response->assertStatus(200);
    $this->assertDatabaseHas('comments', [
      'body' => 'updated body',
      'user_id' => $this->anotherUser->id,
      'post_id' => $post->id,
    ]);
  }

  public function test_validation_works_correctly_when_updating_a_comment()
  {
    $this->anotherUser->update([
      'accepted_at' => date("Y-m-d H:i:s"),
    ]);
    $post = $this->createPost($this->user, $this->post);
    $comment = $this->createComment($this->anotherUser, $post, $this->comment);

    $response = $this->actingAs($this->anotherUser)->patch('/api/posts/' . $post->id . '/comments/' . $comment->id, [
      'body' => '',
    ]);

    $response->assertStatus(400);
    $response->assertJsonStructure([
      'errors' => [
        'body',
      ],
    ]);
  }

  public function test_gets_not_found_if_updating_comment_invalid_id()
  {
    $this->anotherUser->update([
      'accepted_at' => date("Y-m-d H:i:s"),
    ]);
    $post = $this->createPost($this->user, $this->post);

    $response = $this->actingAs($this->anotherUser)->patch('/api/posts/' . $post->id . '/comments/' . 1000, $this->comment);

    $this->assertContains($response->status(), [404, 403]);
  }

  public function test_user_gets_unauthorized_error_when_updating_a_comment_doesnot_belong_to_him()
  {
    $this->user->update([
      'accepted_at' => date("Y-m-d H:i:s"),
    ]);
    $this->anotherUser->update([
      'accepted_at' => date("Y-m-d H:i:s"),
    ]);
    $post = $this->createPost($this->user, $this->post);
    $comment = $this->createComment($this->user, $post, $this->comment);

    $response = $this->actingAs($this->anotherUser)->patch('/api/posts/' . $post->id . '/comments/' . $comment->id, [
      'body' => 'updated body',
    ]);

    $response->assertStatus(403);
    $response->assertJsonStructure([
      'message',
    ]);
  }

  public function test_user_can_delete_his_comment()
  {
    $this->user->update([
      'accepted_at' => date("Y-m-d H:i:s"),
    ]);
    $post = $this->createPost($this->user, $this->post);
    $comment = $this->createComment($this->user, $post, $this->comment);

    $response = $this->actingAs($this->user)->delete('/api/posts/' . $post->id . '/comments/' . $comment->id);

    $response->assertStatus(200);
    $response->assertJsonStructure([
      'message',
    ]);
  }

public function test_gets_not_found_if_deleting_comment_invalid_id()
  {
    $this->user->update([
      'accepted_at' => date("Y-m-d H:i:s"),
    ]);
    $post = $this->createPost($this->user, $this->post);

    $response = $this->actingAs($this->user)->delete('/api/posts/' . $post->id . '/comments/' . 1000);

    $this->assertContains($response->status(), [404, 403]);
  }

  public function test_user_gets_unauthorized_error_when_deleting_a_comment_doesnot_belong_to_him()
  {
    $this->user->update([
      'accepted_at' => date("Y-m-d H:i:s"),
    ]);
    $this->anotherUser->update([
      'accepted_at' => date("Y-m-d H:i:s"),
    ]);
    $post = $this->createPost($this->user, $this->post);
    $comment = $this->createComment($this->user, $post, $this->comment);

    $response = $this->actingAs($this->anotherUser)->delete('/api/posts/' . $post->id . '/comments/' . $comment->id);

    $response->assertStatus(403);
    $response->assertJsonStructure([
      'message',
    ]);
  }

  public function test_super_admin_and_admin_can_delete_any_post()
  {
    $this->user->update([
      'accepted_at' => date("Y-m-d H:i:s"),
    ]);
    $post = $this->createPost($this->user, $this->post);
    $comment = $this->createComment($this->user, $post, $this->comment);

    $response = $this->actingAs($this->superAdmin)->delete('/api/posts/' . $post->id . '/comments/' . $comment->id);

    $response->assertStatus(200);
    $response->assertJsonStructure([
      'message',
    ]);
    $this->assertDatabaseMissing('posts', [
      'id' => $comment->id,
    ]);
  }
}
