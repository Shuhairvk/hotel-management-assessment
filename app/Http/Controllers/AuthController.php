<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $r)
    {
        $r->validate(['email' => 'required|email', 'password' => 'required', 'role' => 'required|in:admin,staff']);
        $u = User::where('email', $r->email)->first();
        if (!$u || !Hash::check($r->password, $u->password) || $u->role !== $r->role || !$u->is_active) {
            return response()->json(['message' => 'Invalid credentials'], 422);
        }
        return ['token' => $u->createToken('app')->plainTextToken, 'user' => $u];
    }

    public function me(Request $r) { return $r->user(); }

    public function logout(Request $r)
    {
        $r->user()->currentAccessToken()->delete();
        return ['message' => 'Logged out'];
    }
}
