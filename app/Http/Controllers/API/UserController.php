<?php
namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function index()
    {
        try {
            Gate::authorize('view-users');
            $users = User::with('roles:id,name')->get();
            return response()->json([
                'status' => 'success',
                'data'   => UserResource::collection($users),
            ]);

        } catch (AuthorizationException $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Unauthorized access',
            ], 403);

        } catch (\Exception $e) {
            Log::error('User index error: ' . $e->getMessage());
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to load user data',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    public function assignRole(Request $request, $id)
    {
        try {
            Gate::authorize('assign-roles');

            $user = User::findOrFail($id);
            $data = $request->validate(['role' => 'required|string|exists:roles,name']);

            $user->assignRole($data['role']);

            return response()->json([
                'message' => 'Role assigned successfully',
                'user'    => $user->load('roles'),
            ]);

        } catch (\Exception $e) {
            $status  = 500;
            $message = 'Failed to assign role';

            if ($e instanceof AuthorizationException) {
                $status  = 403;
                $message = 'You lack permissions to assign roles';
            } elseif ($e instanceof ModelNotFoundException) {
                $status  = 404;
                $message = 'User or role not found';
            } elseif ($e instanceof ValidationException) {
                $status  = 422;
                $message = 'Validation failed';
            }

            return response()->json([
                'message' => $message,
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], $status);
        }
    }

    public function profile(Request $request)
    {
        try {
            $user = $request->user()->load('roles');
            return response()->json([
                'status'      => 'success',
                'status_code' => 200,
                'data'        => new UserResource($user),
            ]);
        } catch (\Exception $e) {
            Log::error('Profile retrieval error: ' . $e->getMessage());
            return response()->json([
                'status'      => 'error',
                'message'     => 'Failed to retrieve profile',
                'error'      => config('app.debug') ? $e->getMessage() : null,
            ]);
        }
    }
}
