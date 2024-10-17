<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleChecker
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $admin, $customer): Response
    {
        $roles[] = Auth::user()->userRoleId;

        if (in_array($admin, $roles)) {
            return $next($request);
        }
        else if (in_array($customer, $roles)) {
            return $next($request);
        }


        return response()->json(['success' => false,'message' => 'Unauthorized'], 401);
    }
}
