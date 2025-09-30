<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Responses\GeneralResponse\Response;
use App\Http\Responses\GeneralResponse\ResultBuilder;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    private $response, $responseBuilder;

    protected $userRepository;

    function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;

        $this->response = new Response;
        $this->responseBuilder = new ResultBuilder;
    }

    public function showLoginForm()
    {
        return view('auth.signin');
    }

    public function attemptLogin(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => ['required', 'email'],
                'password' => ['required', 'string'],
            ]);

            if ($validator->fails()) {
                $this->responseBuilder->setStatus(false);
                $this->responseBuilder->setMessage($validator->errors()->first());

                return $this->response->generateResponse($this->responseBuilder);
            }

            // Get user by email
            $user = $this->userRepository->findByEmail($request->email);
            
            // Check if user exists and password is correct
            if (!$user || !Hash::check($request->password, $user->password)) {
                $this->responseBuilder->setStatus(false);
                $this->responseBuilder->setMessage("The provided credentials do not match our records.");

                return $this->response->generateResponse($this->responseBuilder);
            }

            // Check if user is active
            if ($user->status !== 'active') {
                $this->responseBuilder->setStatus(false);
                $this->responseBuilder->setMessage('Your account has been ' . $user->status . '.');

                return $this->response->generateResponse($this->responseBuilder);
            }

            // Update last login information
            $this->userRepository->updateLastLogin($user->id, $request->ip());

            $this->responseBuilder->setMessage("Login successful.");
            return $this->response->generateResponse($this->responseBuilder);
        } catch (\Throwable $th) {
            $this->responseBuilder->setStatus(false);
            $this->responseBuilder->setMessage($th->getMessage());

            return $this->response->generateResponse($this->responseBuilder);
        }
    }
}
