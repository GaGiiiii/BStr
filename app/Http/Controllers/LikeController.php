<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\Post;
use Exception;
use Illuminate\Http\Request;
use Validator;

class LikeController extends Controller {
  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request) {
    try {
      $post = Post::find($request->post_id);

      if (!$post) {
        return response([
          'like' => null,
          'message' => 'Post not found.',
        ], 400);
      }

      // VALIDATE DATA
      $validator = Validator::make($request->all(), [
        'post_id' => 'required|integer',
      ]);

      if ($validator->fails()) {
        return response([
          'like' => null,
          'message' => 'Validation failed.',
          'errors' => $validator->messages(),
        ], 400);
      }

      $like = new Like;

      $like->user_id = auth()->user()->id;
      $like->post_id = $request->post_id;

      $like->save();
      $like->fresh(['user', 'post']);

      return response([
        "like" => $like,
        "message" => "Like created.",
      ], 201);
    } catch (Exception $e) {
      return response([
        "like" => null,
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
    $like = Like::find($id);

    if (!$like) {
      return response([
        'like' => null,
        'message' => 'Like not found.',
      ], 400);
    }

    if (auth()->user()->cannot('delete', $like)) {
      return response([
        'like' => $like,
        'message' => 'Unauthorized',
      ], 401);
    }

    $like->delete();

    return response([
      "like" => $like,
      "message" => "Like deleted.",
    ], 200);
  }
}
