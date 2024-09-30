<?php
namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    
    protected $except = [
            'api/csrf-cookie',

        // Add any routes you want to exclude from CSRF protection, if needed
    ];

    public function handle($request, Closure $next)
    {
        // Log CSRF tokens for debugging
        if ($request->is('admin/login')) {
            Log::info('CSRF Token in request:', ['token' => $request->header('X-XSRF-TOKEN')]);
            Log::info('CSRF Token in session:', ['token' => $request->session()->token()]);
        }

        return parent::handle($request, $next);
    }
}
