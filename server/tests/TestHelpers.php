<?php

namespace Tests;

use App\Models\User;

trait TestHelpers
{
  public function createUserWithRole($role)
  {
    $user = User::create([
      'name' => 'Some Name',
      'email' => fake()->unique()->safeEmail(),
      'password' => bcrypt('12345678'),
    ]);

    $user->assignRole($role);

    return $user;
  }

  public function createPost($user, $post)
  {
    return $user->posts()->create($post);
  }

  public function createComment($user, $post, $comment)
  {
    return $post->comments()->create(array_merge($comment, [
      'post_id' => $post->id,
      'user_id' => $user->id,
    ]));
  }
}
