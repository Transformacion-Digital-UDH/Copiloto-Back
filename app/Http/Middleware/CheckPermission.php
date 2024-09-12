<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckPermission
{
    public function handle(Request $request, Closure $next, $permission)
    {
        \Log::info('Checking permission', ['user_id' => auth()->id(), 'permission' => $permission]);
        
        if (!auth()->user()->hasPermission($permission)) {
            return response()->json(['message' => 'Unauthorized jojojo'], 403);
        }

        return $next($request);
    }
}

