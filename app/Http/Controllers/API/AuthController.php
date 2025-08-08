<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $user->giveRole('author');

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'User registered successfully',
            'user'    => $user,
            'token'   => $token,
        ], 201);
    }

    public function login(LoginRequest $request) {
        $user = User::where('email', $data['email'])->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages(['email' => ['The provided credentials are incorrect.']]);
        }

        $user->tokens()->delete();

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'User logged in successfully!',
            'user'    => $user,
            'token'   => $token
        ]);
    }

        public function logout(Request $request) {
        $user = $request->user();
        $user->currentAccessToken()->delete();
        return response()->json(['message'=>'Logged out']);
    }

}
