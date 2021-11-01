<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentFactory extends Factory {
  /**
   * Define the model's default state.
   *
   * @return array
   */
  public function definition() {
    $users = User::all();
    $posts = Post::all();

    return [
      'post_id' => $posts[rand(0, sizeof($posts) - 1)],
      'user_id' => $users[rand(0, sizeof($users) - 1)],
      'body' => $this->faker->text('1000'),
    ];
  }
}
