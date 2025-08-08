<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function index()
    {
        Gate::authorize('view-users');
        return User::with('roles')->paginate(20);
    }

    public function assignRole(Request $request, $id)
    {
        Gate::authorize('assign-roles');

        $user = User::findOrFail($id);
        $data = $request->validate([
            'role' => 'required|string|exists:roles,name',
        ]);

        $role = Role::where('name', $data['role'])->firstOrFail();
        $user->giveRole($role);

        return response()->json(['message' => 'Role assigned', 'user' => $user->load('roles')]);
    }

      public function profile(Request $request) {
        $user = $request->user()->load('roles');
        return $user;
    }
}
