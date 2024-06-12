<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApiRequests\UserRequests\LoginUserRequest;
use App\Http\Requests\ApiRequests\UserRequests\StoreUserRequest;
use App\Http\Requests\ApiRequests\UserRequests\UpdateAvatarUserRequest;
use App\Http\Requests\ApiRequests\UserRequests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function login(LoginUserRequest $request)
    {
        if (User::where('email', $request->email)
            ->where('password', md5($request->password))->first()) {
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

    public function store(StoreUserRequest $request)
    {

        if ($request->hasFile('avatar')) {
            $avatar = $request->file('avatar');
            $path = $avatar->store('avatars', 'public');
            $userData = $request->except('avatar');
            $userData['avatar'] = $path;
            $userData['password'] = md5($request->password);
            $createdUser = User::create($userData);
        }else{
            $createdUser['password'] = md5($request->password);
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

    public function updateAvatar(UpdateAvatarUserRequest $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
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

    public function update(UpdateUserRequest $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
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
