<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;  // step one

class CommentTest extends TestCase {
  use RefreshDatabase;
  use WithFaker;

  public function test_get_all_comments() {
    $comments = Comment::factory()->count(2)->create();

    $response = $this->get('/api/comments');
    $response
      ->assertStatus(200)
      ->assertJson([
        "comments" => $comments->toArray(),
        "message" => "Comments found",
      ]);
  }

  public function test_get_all_comments_no_comments() {
    $response = $this->get('/api/comments');
    $response
      ->assertStatus(200)
      ->assertJson([
        "comments" => [],
        "message" => "Comments found",
      ]);
  }

  public function test_add_new_comment() {
    $user = User::factory()->create();
    $category = Category::factory()->create();
    $post = Post::factory()->for($user)->for($category)->create();
    $this->actingAs($user);

    $response = $this->postJson('/api/comments', [
      'body' => $this->faker->text(50),
      'post_id' => $post->id
    ]);

    $response->assertStatus(201);
  }

  public function test_add_new_comment_no_user() {
    $post = Post::factory()->create();

    $response = $this->postJson('/api/comments', [
      'body' => $this->faker->text(30),
      'post_id' => $post->id
    ]);

    $response->assertStatus(401)
      ->assertJson([
        'message' => 'Unauthenticated.'
      ]);
  }

  public function test_add_new_comment_no_post() {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->postJson('/api/comments', [
      'body' => $this->faker->text(30),
    ]);

    $response->assertStatus(400)
      ->assertJson([
        'comment' => null,
        'message' => 'Post not found.',
      ]);
  }

  public function test_update_comment() {
    $user = User::factory()->create();
    $category = Category::factory()->create();
    $post = Post::factory()->for($user)->for($category)->create();
    $comment = Comment::factory()->for($user)->for($post)->create();
    $this->actingAs($user);

    $fakeBody =  $this->faker->text(35);

    $response = $this->putJson("/api/comments/$comment->id", [
      'body' => $fakeBody,
      'post_id' => $post->id
    ]);

    $comment->body = $fakeBody;

    $response
      ->assertStatus(200)
      ->assertJson([
        "comment" => $comment->toArray(),
        "message" => "Comment updated.",
      ]);
  }

  public function test_update_comment_unauthorized() {
    $user = User::factory()->create();
    $user2 = User::factory()->create();
    $category = Category::factory()->create();
    $post = Post::factory()->for($category)->for($user)->create();
    $comment = Comment::factory()->for($user)->for($post)->create();

    $this->actingAs($user2);

    $response = $this->putJson("/api/comments/$comment->id", [
      'body' => $this->faker->text(35),
      'post_id' => $post->id
    ]);

    $response
      ->assertStatus(401)
      ->assertJson([
        "comment" => $comment->toArray(),
        "message" => "Unauthorized.",
      ]);
  }

  public function test_delete_comment() {
    $user = User::factory()->create();
    $category = Category::factory()->create();
    $post = Post::factory()->for($user)->for($category)->create();
    $comment = Comment::factory()->for($user)->for($post)->create();
    $this->actingAs($user);

    $response = $this->delete("/api/comments/$comment->id");
    $response
      ->assertStatus(200)
      ->assertJson([
        "comment" => $comment->toArray(),
        "message" => "Comment deleted.",
      ]);
  }

  public function test_delete_comment_which_doesnt_exist() {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->delete("/api/comments/1");
    $response->assertStatus(404)
      ->assertJson([
        'comment' => null,
        'message' => 'Comment not found.',
      ]);
  }

  public function test_delete_comment_unauthorized() {
    $user = User::factory()->create();
    $user2 = User::factory()->create();
    $category = Category::factory()->create();
    $post = Post::factory()->for($category)->for($user)->create();
    $comment = Comment::factory()->for($user)->for($post)->create();

    $this->actingAs($user2);

    $response = $this->delete("/api/comments/$comment->id");
    $response
      ->assertStatus(401)
      ->assertJson([
        "comment" => $comment->toArray(),
        "message" => "Unauthorized.",
      ]);
  }
}
