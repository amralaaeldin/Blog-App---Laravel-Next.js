<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Tests\TestHelpers;
use Tests\TestCase;

class TagTest extends TestCase
{
  use RefreshDatabase;
  use TestHelpers;

  private User $user;
  private $post;

  protected function setUp(): void
  {
    parent::setUp();

    $this->user = $this->createUserWithRole('user');

    $this->post = [
      'title' => fake()->sentence,
      'body' => fake()->paragraph,
      'user_id' => $this->user->id,
      'tagNames' => ['tag1', 'tag2'],
    ];
  }

  public function test_any_user_can_get_all_tags()
  {
    $response = $this->get('/api/tags');

    $response->assertStatus(200);
    $response->assertJsonStructure([
      '*' => [
        'id',
        'name',
      ],
    ]);
  }

  public function test_any_user_can_get_posts_on_a_tag()
  {
    $this->user->update([
      'accepted_at' => date("Y-m-d H:i:s"),
    ]);
    $response = $this->actingAs($this->user)->post('/api/posts', $this->post);

    $response = $this->get('/api/tags/' . \App\Models\Tag::first()->name);

    $response->assertStatus(200);
    $response->assertJsonStructure([
      "current_page",
      "data" => [
        "*" => [
          "id",
          "title",
          "body",
          "user_id",
        ]
      ],
      "first_page_url",
      "last_page",
      "last_page_url",
      "next_page_url",
      "prev_page_url",
      "total",
    ]);
  }
}
