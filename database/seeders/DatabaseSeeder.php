<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Interest;
use App\Models\Like;
use App\Models\Post;
use Illuminate\Database\Seeder;
use \App\Models\User;

class DatabaseSeeder extends Seeder {
  /**
   * Seed the application's database.
   *
   * @return void
   */
  public function run() {
    User::factory()->count(50)->create();
    Category::factory()->count(10)->create();
    Post::factory()->count(70)->create();

    $interests = Interest::factory()->count(100)->make();
    foreach ($interests as $record) {
      Interest::firstOrCreate([
        'user_id' => $record->user_id,
        'category_id' => $record->category_id
      ]);
    }

    Comment::factory()->count(100)->create();
    
    $likes = Like::factory()->count(100)->make();
    foreach ($likes as $record) {
      Like::firstOrCreate([
        'user_id' => $record->user_id,
        'post_id' => $record->post_id
      ]);
    }
  }
}
