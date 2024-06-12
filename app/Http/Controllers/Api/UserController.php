<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => 'The email field is required.',
            'email.email' => 'The email must be a valid email address.',
            'password.required' => 'The password field is required.',
        ]);

        if ($validator->fails()) {
            return response()->json(["message" => "Validation error!", 'errors' => $validator->errors()], 422,);
        }


        if (User::where('email', $request->email)
            ->where('password', $request->password)->first()) {
            $token = User::where(['email' => $request->email])->first()->generateToken();
            return response()->json(['data' => ['token' => $token]]);
        } else {
            return response()->json(['data' => ['message' => 'login failed']], 401);
        }
    }

    public function logout()
    {
        Auth::user()->logout();
        return response()->json(['data' => ['message' => 'logout']]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
            'avatar' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
        ], [
            'name.required' => 'The name field is required.',
            'name.string' => 'The name must be a string.',
            'name.max' => 'The name may not be greater than 255 characters.',
            'email.required' => 'The email field is required.',
            'email.email' => 'The email must be a valid email address.',
            'email.max' => 'The email may not be greater than 255 characters.',
            'email.unique' => 'The email has already been taken.',
            'password.required' => 'The password field is required.',
            'password.string' => 'The password must be a string.',
            'password.min' => 'The password must be at least 8 characters.',
        ]);

        if ($validator->fails()) {
            return response()->json(["message" => "Validation error!", 'errors' => $validator->errors()], 422);
        }

        if ($request->hasFile('avatar')) {
            $avatar = $request->file('avatar');
            $path = $avatar->store('avatars', 'public');
            $userData = $request->except('avatar');
            $userData['avatar'] = $path;
            $createdUser = User::create($userData);
        }else{
            $createdUser = User::create($request->all());
        }

        return response()->json(["message" => "User created!", "data" => $createdUser], 201);
    }
    public function avatar()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        $avatar = $user->avatar;
        $filePath = storage_path('app/public/'. $avatar);
        return response()->file($filePath);
    }

    public function updateAvatar(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ], [
            'avatar.required' => 'The avatar field is required.',
            'avatar.image' => 'The avatar must be an image.',
            'avatar.mimes' => 'The avatar must be a file of type: jpeg, png, jpg, gif.',
            'avatar.max' => 'The avatar may not be greater than 2048 kilobytes.'
        ]);

        if ($validator->fails()) {
            return response()->json(["message" => "Validation error!", 'errors' => $validator->errors()], 422);
        }

        if ($request->hasFile('avatar')) {
            $avatar = $request->file('avatar');
            $path = $avatar->store('avatars', 'public');
            $user->avatar = $path;
            $user->save();

            return response()->json([
                "message" => "Avatar updated!",
                "data" => $user
            ]);
        } else {
            return response()->json(['message' => 'Avatar not provided'], 400);
        }
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'email' => 'email|max:255|unique:users,email,',
            'password' => 'string|min:8',
        ], [
            'name.string' => 'The name must be a string.',
            'name.max' => 'The name may not be greater than 255 characters.',
            'email.email' => 'The email must be a valid email address.',
            'email.max' => 'The email may not be greater than 255 characters.',
            'email.unique' => 'The email has already been taken.',
            'password.string' => 'The password must be a string.',
            'password.min' => 'The password must be at least 8 characters.',
        ]);

        if ($validator->fails()) {
            return response()->json(["message" => "Validation error!", 'errors' => $validator->errors()], 422);
        }

        $user->update($request->all());

        return response()->json([
            "message" => "User updated!",
            "data" => $user
        ]);
    }

    public function destroy()
    {
        $user = Auth::user();
        $user->delete();
        return response()->json(['message' => 'User deleted']);
    }
}
