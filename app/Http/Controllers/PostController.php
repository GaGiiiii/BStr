<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Validator;

class PostController extends Controller {
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request) {
    $sortBy = $request->query('sortBy');
    $categories = $request->query('categories'); // Eg. categories=1,3,5,6
    $search = $request->query('search');

    $posts = Post::with(['likes', 'comments', 'category', 'user'])
      ->whereRelation('user', 'first_name', 'LIKE', "%$search%")
      ->orWhereRelation('user', 'last_name', 'LIKE', "%$search%");

    switch ($sortBy) {
      case 'dateDesc':
        $posts = $posts->orderBy('created_at', 'desc')->get();
        break;
      case 'dateAsc':
        $posts = $posts->orderBy('created_at', 'asc')->get();
        break;
      case 'popularity':
        $posts = $posts->get()->toArray();
        usort($posts, function ($a, $b) {
          return (sizeof($b['comments']) + sizeof($b['likes'])) -  (sizeof($a['comments']) + sizeof($a['likes']));
        });
        break;
      default:
        $posts = $posts->orderBy('id', 'desc')->get();
    }

    $categoriesArr = array_filter(explode(",", $categories));

    if (!is_array($posts)) {
      $posts = $posts->toArray();
    }

    if (sizeof($categoriesArr) !== 0) {
      $posts = array_filter($posts, function ($post) use ($categoriesArr) {
        return in_array($post['category_id'], $categoriesArr);
      });
      $posts = array_values($posts); // Fix Array
    }

    return response([
      "num_of_posts" => sizeof($posts),
      "posts" => $posts,
      "message" => "Posts found",
    ], 200);
  }

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
          'post' => null,
          'message' => 'Category not found.',
        ], 400);
      }

      // VALIDATE DATA
      $validator = Validator::make($request->all(), [
        'category_id' => 'required|integer',
        'title' => 'required|string|min:10|max:100',
        'body' => 'string|min:10|max:10000',
        'image' => 'image|file|max:5000',
        'video' => 'file|mimes:mp4,mov,ogg,qt,webm,oga,ogv,ogx|max:20000',
      ]);

      if (!$request->body && !$request->file('image') && !$request->file('video')) {
        throw ValidationException::withMessages(['body' => 'Please either enter text or add photo / video.']);
      }

      if ($validator->fails()) {
        return response([
          'post' => null,
          'message' => 'Validation failed.',
          'errors' => $validator->messages(),
        ], 400);
      }

      return 1;

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
    } catch (ValidationException $e) {
      return response([
        "post" => null,
        'message' => 'Validation failed.',
        "errors" => $e->errors(),
      ], 500);
    } catch (Exception $e) {
      return response([
        "post" => null,
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
