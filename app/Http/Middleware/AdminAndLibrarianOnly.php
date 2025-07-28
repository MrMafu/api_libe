<?php

namespace App\Http\Middleware;

use App\Http\Api\ApiResponse;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminAndLibrarianOnly
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::guard("sanctum")->user();
        $userRole = User::query()->find($user->id)->role;

        if ($userRole !== "admin" && $userRole !== "librarian")
        {
            return ApiResponse::error("Forbidden.", 403);
        }

        return $next($request);
    }
}
