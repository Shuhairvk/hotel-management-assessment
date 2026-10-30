<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckPermission
{
    // usage: permission:rooms  |  permission:admin
    public function handle(Request $request, Closure $next, string $module)
    {
        $u = $request->user();
        if ($u && $u->is_active && ($u->role === 'admin' || in_array($module, $u->permissions ?? []))) {
            return $next($request);
        }
        return response()->json(['message' => 'Forbidden'], 403);
    }
}
