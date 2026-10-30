<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StaffController extends Controller
{
    public function index()
    {
        return User::where('role', 'staff')->latest('id')->paginate(10);
    }

    public function store(Request $r)
    {
        $d = $r->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'permissions' => 'array',
            'permissions.*' => 'in:bookings,rooms,guests',
            'is_active' => 'boolean',
        ]);
        return User::create($d + ['role' => 'staff']);
    }

    public function update(Request $r, User $staff)
    {
        abort_if($staff->role !== 'staff', 404);
        $d = $r->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($staff->id)],
            'password' => 'nullable|min:6',
            'permissions' => 'array',
            'permissions.*' => 'in:bookings,rooms,guests',
            'is_active' => 'boolean',
        ]);
        if (empty($d['password'])) unset($d['password']);
        $staff->update($d);
        return $staff;
    }

    public function destroy(User $staff)
    {
        abort_if($staff->role !== 'staff', 404);
        $staff->tokens()->delete();
        $staff->delete();
        return response()->noContent();
    }
}
