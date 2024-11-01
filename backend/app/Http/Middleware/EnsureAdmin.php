<?php

namespace App\Http\Middleware;

use App\Exceptions\RoleExceptions;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Constants\Role;

class EnsureAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     * @throws RoleExceptions
     */
    public function handle(Request $request, Closure $next): Response
    {
        $current_user = $request->user();
        if($current_user->role !== Role::ADMIN) {
            throw RoleExceptions::unAuthorized();
        }
        return $next($request);
    }
}
