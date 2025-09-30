<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    protected $userRepository;

    public function __construct(
        UserRepositoryInterface $userRepository
    ) {
        $this->userRepository = $userRepository;
    }
    public function showLoginForm()
    {
        return view('auth.signin');
    }

    /**
     * Handle logout
     */
    public function logout(Request $request)
    {
        // Get token from cookie
        $token = $request->cookie('auth_token');

        if ($token) {
            // Revoke token from database
            $this->userRepository->revokeToken($token);
        }

        // Redirect with cookie deletion
        return redirect('/login')
            ->with('success', 'Logged out successfully.')
            ->cookie('auth_token', '', -1, '/', null, false, false); // Delete cookie
    }
}
