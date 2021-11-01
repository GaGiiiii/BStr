<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
    // VALIDATE DATA
    $validator = Validator::make($request->all(), [
      'first_name' => 'required|alpha|min:2',
      'last_name' => 'required|alpha|min:2',
      'email' => 'required|string|email|unique:users,email',
      'password' => 'required|string|min:4|confirmed',
      'image' => 'file|image|max:5000',
    ]);

    if ($validator->fails()) {
      return response([
        'user' => null,
        'message' => 'Validation failed.',
        'errors' => $validator->messages(),
      ], 400);
    }

    $user = new User;

    $user->first_name = $request->first_name;
    $user->last_name = $request->last_name;
    $user->email = $request->email;
    $user->password = Hash::make($request->password);

    // CHECK IF IMAGE IS UPLOADED
    if (isset($request->image)) {
      // CREATE UNIQUE FILENAME AND STORE IT UNIQUE FOLDER
      $fileName = $user->first_name . "_" . $user->last_name . "_" . date('dmY_Hs') . "." . $request->image->extension() ?? null;
      $path = $request->file('image')->storeAs('avatars/' . $user->username, $fileName);
      $user->image = $fileName;
    }

    $user->save();
    $token = $user->createToken('usertoken');

    return response([
      "user" => $user,
      "message" => "User created.",
      'token' => $token->plainTextToken,
    ], 201);
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
}
