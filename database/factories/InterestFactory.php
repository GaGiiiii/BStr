<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class InterestFactory extends Factory {
  /**
   * Define the model's default state.
   *
   * @return array
   */
  public function definition() {
    $users = User::all();
    $categories = Category::all();

    return [
      'user_id' => $users[rand(0, sizeof($users) - 1)],
      'category_id' => $categories[rand(0, sizeof($categories) - 1)],
    ];
  }
}
