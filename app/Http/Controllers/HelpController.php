<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
}
