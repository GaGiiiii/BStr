<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory {
  /**
   * Define the model's default state.
   *
   * @return array
   */
  public function definition() {
    $users = User::all();
    $categories = Category::all();

    return [
      // 'user_id' => $users[rand(0, sizeof($users) - 1)],
      'user_id' => User::factory(),
      'category_id' => Category::factory(),
      // 'category_id' => $categories[rand(0, sizeof($categories) - 1)],
      'title' => $this->faker->unique()->sentence(),
      'body' => $this->faker->text('1000'),
    ];
  }
}
