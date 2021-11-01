<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Request;

class CategoryController extends Controller {
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

    $posts = Post::where('category_id', $id)->with(['likes', 'comments', 'user', 'category'])->get();

    return response([
      "posts" => $posts,
      "message" => "Posts found",
    ], 200);
  }
}
