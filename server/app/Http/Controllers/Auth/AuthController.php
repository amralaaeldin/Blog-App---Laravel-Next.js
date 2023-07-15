<?php

namespace App\Http\Controllers\Auth;

use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Controllers\Controller;
use App\Models\User;


class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
            ]);

            $user->assignRole('user');
            $token = auth()->login($user);
        } catch (\Exception $e) {
            throw new \App\Exceptions\QueryDBException(__('An error occurred while retrieving.'));
        }

        return response()->json([
            'token' => $this->respondWithToken($token),
            'user' => $user,
        ], 201);
    }

    public function login(LoginRequest $request)
    {
        try {
            $credentials = request(['email', 'password']);

            if (!$token = auth()->attempt($credentials)) {
                return response()->json(['msessage' => __('The provided credentials are incorrect.')], 401);
            }
        } catch (\Exception $e) {
            throw new \App\Exceptions\QueryDBException(__('An error occurred while retrieving.'));
        }

        return response()->json([
            'token' => $this->respondWithToken($token),
            'user' => auth()->user(),
        ]);
    }

    public function logout()
    {
        try {
            auth()->logout();
        } catch (\Exception $e) {
            throw new \App\Exceptions\QueryDBException(__('An error occurred while retrieving.'));
        }
        return response()->json(['message' => __('Logged out successfully.')]);
    }

    public function refresh()
    {
        try {
            return response()->json($this->respondWithToken(auth()->refresh()));
        } catch (\Exception $e) {
            throw new \App\Exceptions\QueryDBException(__('An error occurred while retrieving.'));
        }
    }

    protected function respondWithToken($token)
    {
        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_at' => now()->addMinutes(auth()->factory()->getTTL())->toDateTimeString()
        ];
    }
}
