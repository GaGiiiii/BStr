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

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, $id) {
    try {
      $post = Post::find($id);
      $category = Category::find($request->category_id);

      if (!$post || !$category) {
        return response([
          'post' => null,
          'message' => 'Post / Category not found.',
        ], 404);
      }

      if (auth()->user()->cannot('update', $post)) {
        return response([
          "post" => $post,
          "message" => "Unauthorized.",
        ], 401);
      }

      // VALIDATE DATA
      $validator = Validator::make($request->all(), [
        'category_id' => 'required|integer',
        'title' => 'required|string|min:10|max:100',
        'body' => 'required|string|min:10|max:10000',
      ]);

      if ($validator->fails()) {
        return response([
          'post' => $post,
          'message' => 'Validation failed.',
          'errors' => $validator->messages(),
        ], 400);
      }

      $post->category_id = $request->category_id;
      $post->title = $request->title;
      $post->body = $request->body;
      $post->save();

      $post = $post->fresh(['likes', 'comments', 'category', 'comments.user', 'user']);

      return response([
        "post" => $post,
        "message" => "Post updated.",
      ], 200);
    } catch (Exception $e) {
      return response([
        "post" => $post,
        "message" => $e->getMessage(),
      ], 500);
    }
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id) {
    try {
      $post = Post::find($id);

      if (!$post) {
        return response([
          'post' => null,
          'message' => 'Post not found.',
        ], 404);
      }

      if (auth()->user()->cannot('delete', $post)) {
        return response([
          "post" => $post,
          "message" => "Unauthorized.",
        ], 401);
      }

      $post->delete();

      return response([
        "post" => $post,
        "message" => "Post deleted.",
      ], 200);
    } catch (Exception $e) {
      return response([
        "post" => $post,
        "message" => $e->getMessage(),
      ], 500);
    }
  }
}
