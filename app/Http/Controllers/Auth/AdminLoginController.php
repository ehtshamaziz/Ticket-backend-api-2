<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class AdminLoginController extends Controller
{
    /**
     * Show the admin login form.
     */

    public function showLoginForm()
    {
        // return "echooo";
        return view('auth.admin-login'); // Create a view for the admin login form
    }

    /**
     * Handle the admin login request.
     */
    public function login(Request $request)
    {
        echo "Hello, World!";

   if ($request->isMethod('options')) {
        return response()->json(['status' => 'OK'], 200);
    }

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::guard('admin')->attempt($credentials)) {
            $request->session()->regenerate();

            return response()->json(['message' => 'Login successful'], 200);

            // return redirect()->intended(route('admin.dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    /**
     * Logout the admin.
     */
    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/admin/login');
    }
}
