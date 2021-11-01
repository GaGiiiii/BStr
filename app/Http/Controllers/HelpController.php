<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;

class HelpController extends Controller {
  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function mostPopularPosts($id) {
    $category = Category::find($id);

    if (!$category) {
      return response([
        'category' => null,
        'message' => 'Category not found.',
      ], 404);
    }

    $posts = Post::where('category_id', $id)->with(['likes', 'comments', 'user', 'category'])->get()->toArray();

    usort($posts, function ($a, $b) {
      return (sizeof($b['comments']) + sizeof($b['likes'])) -  (sizeof($a['comments']) + sizeof($a['likes']));
    });

    return response([
      "posts" => $posts,
      "message" => "Posts found",
    ], 200);
  }

  public function usersPoints($id) {
    $user = User::find($id);

    if (!$user) {
      return response([
        'user' => null,
        'message' => 'User not found.',
      ], 404);
    }

    $points = 0;

    $posts = Post::where('user_id', $id)->with(['likes'])->get();

    foreach ($posts as $post) {
      $points += sizeof($post->likes);
    }

    return response([
      "points" => $points,
      "message" => "Posts found",
    ], 200);
  }
}
