<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        echo "Hello, World!";

        if (Auth::guard('admin')->check()) {
            return $next($request); // Proceed if the user is authenticated as an admin
        }

        return redirect('/admin/login'); // Redirect to admin login if not authenticated
    }
}
