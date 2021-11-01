<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use Exception;
use Illuminate\Http\Request;
use Validator;

class PostController extends Controller {
  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request) {
    try {
      $category = Category::find($request->category_id);

      if (!$category) {
        return response([
          'product' => null,
          'message' => 'Category not found.',
        ], 400);
      }

      // VALIDATE DATA
      $validator = Validator::make($request->all(), [
        'category_id' => 'required|integer',
        'title' => 'required|string|min:10|max:100',
        'body' => 'required|string|min:10|max:10000',
      ]);

      if ($validator->fails()) {
        return response([
          'product' => null,
          'message' => 'Validation failed.',
          'errors' => $validator->messages(),
        ], 400);
      }

      $post = new Post;
      $post->category_id = $request->category_id;
      $post->title = $request->title;
      $post->body = $request->body;
      $post->user_id = auth()->user()->id;
      $post->save();

      $post = $post->fresh(['likes', 'comments', 'category', 'comments.user', 'user']);

      return response([
        "post" => $post,
        "message" => "Post created.",
      ], 201);
    } catch (Exception $e) {
      return response([
        "post" => $post,
        "message" => $e->getMessage(),
      ], 500);
    }
  }
}
