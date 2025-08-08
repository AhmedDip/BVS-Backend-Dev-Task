<?php
namespace App\Http\Controllers\API;

use App\Models\User;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\RegisterRequest;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{

    public function register(RegisterRequest $request)
    {
        try {
            $validated = $request->validated();

            $user = User::create([
                'name'     => $validated['name'],
                'email'    => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);

            $user->assignRole('author');

            $token = $user->createToken('api-token')->plainTextToken;

            return response()->json([
                'status'      => 'success',
                'status_code' => 201,
                'message'     => 'User registered successfully!',
                'user'        => $user,
                'token'       => $token,
            ], 201);

        } catch (\Exception $e) {
            Log::error('Registration Error: ' . $e->getMessage());

            return response()->json([
                'status'  => 'error',
                'status_code' => 500,
                'message' => 'Registration failed',
                'error'   => config('app.debug') ? $e->getMessage() : 'Internal server error',
            ], 500);
        }
    }

    public function login(LoginRequest $request)
    {
        try {
            $data = $request->validated();

            $user = User::where('email', $data['email'])->first();

            if (! $user || ! Hash::check($data['password'], $user->password)) {
                throw ValidationException::withMessages(['email' => ['The provided credentials are incorrect.']]);
            }

            $user->tokens()->delete();

            $token = $user->createToken('api-token')->plainTextToken;

            return response()->json([
                'status'  => 'success',
                'status_code' => 200,
                'message' => 'User logged in successfully!',
                'user'    => $user,
                'token'   => $token,
            ]);
        } catch (\Exception $e) {
            Log::error('Login Error: ' . $e->getMessage());

            return response()->json([
                'message' => 'Login failed',
                'status'  => 'error',
                'status_code' => 401,
                'error'   => config('app.debug') ? $e->getMessage() : 'Internal server error',
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        $user->currentAccessToken()->delete();
        return response()->json([
            'status'  => 'success',
            'status_code' => 200,
            'message' => 'User logged out successfully!',
        ]);
    }

}
