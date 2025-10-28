<?php

namespace App\Http\Middleware;

use App\Repositories\Contracts\UserRepositoryInterface;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check token in cookie
        $token = $request->cookie('auth_token');

        if ($token) {
            $user = $this->userRepository->findByToken($token);

            if ($user && $user->status === 'active') {
                return redirect('/dashboard');
            }
        }

        return $next($request);
    }
}
