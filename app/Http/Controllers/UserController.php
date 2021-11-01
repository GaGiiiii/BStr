<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Interest;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Ramsey\Uuid\Type\Integer;
use Validator;

class UserController extends Controller {

  public function login(Request $request) {
    // VALIDATE DATA
    $validator = Validator::make($request->all(), [
      'email' => 'required|email',
      'password' => 'required|string',
    ]);

    if ($validator->fails()) {
      return response([
        'user' => null,
        'message' => 'Validation failed.',
        'errors' => $validator->messages(),
      ], 400);
    }

    $user = User::where('email', $request->email)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
      return response([
        "user" => null,
        "message" => "Login failed.",
      ], 401);
    }

    $token = $user->createToken('usertoken');

    $user = $user->fresh(['comments', 'comments.post', 'likes', 'likes.post', 'interests', 'interests.category']);

    return response([
      "user" => $user,
      "message" => "Login successful",
      'token' => $token->plainTextToken,
    ], 200);
  }

  public function logout(Request $request) {
    $user = auth()->user();
    $user->tokens()->delete();

    return response([
      "user" => $user,
      "message" => "Logout successful.",
    ], 200);
  }

  public function register(Request $request) {
    try {
      // VALIDATE DATA
      $validator = Validator::make($request->all(), [
        'first_name' => 'required|alpha|min:2',
        'last_name' => 'required|alpha|min:2',
        'email' => 'required|string|email|unique:users,email',
        'password' => 'required|string|min:4|confirmed',
        'image' => 'required|file|image|max:5000',
        'interests' => 'required|min:5|string', // Min 5 chars because req will be like this 1,7,5. Those are categories Ids
      ]);

      if ($validator->fails()) {
        return response([
          'user' => null,
          'message' => 'Validation failed.',
          'errors' => $validator->messages(),
        ], 400);
      }

      DB::beginTransaction();

      $user = new User;

      $user->first_name = $request->first_name;
      $user->last_name = $request->last_name;
      $user->email = $request->email;
      $user->password = Hash::make($request->password);

      // CHECK IF IMAGE IS UPLOADED
      if (isset($request->image)) {
        // CREATE UNIQUE FILENAME AND STORE IT UNIQUE FOLDER
        $fileName = $user->first_name . "_" . $user->last_name . "_" . date('dmY_Hs') . "." . $request->image->extension() ?? null;
        $path = $request->file('image')->storeAs('avatars/' . $user->email, $fileName);
        $user->image = $fileName;
      }

      $user->save();
      $token = $user->createToken('usertoken');

      // Adding Users Interests
      $interestsArr = explode(",", $request->interests);

      if (!$this->validInterests($interestsArr)) {
        DB::rollBack();

        return response([
          'user' => null,
          'message' => 'Invalid interests.',
        ], 400);
      }

      foreach ($interestsArr as $category_id) {
        $interest = new Interest;

        $interest->user_id = $user->id;
        $interest->category_id = $category_id;

        Interest::firstOrCreate([
          'user_id' => $interest->user_id,
          'category_id' => $interest->category_id,
        ]);
      }

      DB::commit();

      $user = $user->fresh(['comments', 'comments.post', 'likes', 'likes.post', 'interests', 'interests.category']);

      return response([
        "user" => $user,
        "message" => "User created.",
        'token' => $token->plainTextToken,
      ], 201);
    } catch (Exception $e) {
      return response([
        "user" => null,
        "message" => $e->getMessage(),
      ], 500);
    }
  }

  public function update(Request $request, $id) {
    try {
      $user = User::find($id);

      if (!$user) {
        return response([
          'user' => null,
          'message' => 'User not found.',
        ], 404);
      }

      // VALIDATE DATA
      $validator = Validator::make($request->all(), [
        'first_name' => 'required|alpha|min:2',
        'last_name' => 'required|alpha|min:2',
        'email' => 'required|string|email|unique:users,email,' .  auth()->user()->id,
        'password' => 'nullable|string|min:4|confirmed',
        'image' => 'file|image|max:5000',
        'interests' => 'required|min:5|string', // Min 5 chars because req will be like this 1,7,5. Those are categories Ids
      ]);

      if ($validator->fails()) {
        return response([
          'user' => null,
          'message' => 'Validation failed.',
          'errors' => $validator->messages(),
        ], 400);
      }

      DB::beginTransaction();

      $user = new User;

      $user->first_name = $request->first_name;
      $user->last_name = $request->last_name;
      $user->email = $request->email;

      // CHECK IF IMAGE IS UPLOADED
      if (isset($request->image)) {
        // DELETE OLD USER PHOTO
        Storage::deleteDirectory('avatars/' .  auth()->user()->email);

        // CREATE UNIQUE FILENAME AND STORE IT UNIQUE FOLDER
        $fileName = $user->first_name . "_" . $user->last_name . "_" . date('dmY_Hs') . "." . $request->image->extension() ?? null;
        $path = $request->file('image')->storeAs('avatars/' . $user->email, $fileName);
        $user->image = $fileName;
      }

      if (!empty($request->password) && $request->password === $request->password_confirmation) {
        $user->password = Hash::make($request->password);
      }

      $user->save();

      // Delete Users Interests
      Interest::where('user_id', $id)->delete();

      // Adding Users Interests
      $interestsArr = explode(",", $request->interests);

      if (!$this->validInterests($interestsArr)) {
        DB::rollBack();

        return response([
          'user' => null,
          'message' => 'Invalid interests.',
        ], 400);
      }

      foreach ($interestsArr as $category_id) {
        $interest = new Interest;

        $interest->user_id = $user->id;
        $interest->category_id = $category_id;

        Interest::firstOrCreate([
          'user_id' => $interest->user_id,
          'category_id' => $interest->category_id,
        ]);
      }

      DB::commit();

      $user = $user->fresh(['comments', 'comments.post', 'likes', 'likes.post', 'interests', 'interests.category']);

      return response([
        "user" => $user,
        "message" => "User created.",
      ], 201);
    } catch (Exception $e) {
      return response([
        "user" => null,
        "message" => $e->getMessage(),
      ], 500);
    }
  }

  public function loggedIn() {
    if (auth()->user()) {
      return response([
        "user" => auth()->user(),
        "message" => "User logged in.",
      ], 200);
    }

    return response([
      "user" => null,
      "message" => "Not logged in.",
    ], 401);
  }

  private function validInterests(array $interests) {
    foreach ($interests as $category_id) {
      if (!Category::find($category_id)) {
        return false;
      }
    }

    return true;
  }
}
