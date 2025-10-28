<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Responses\GeneralResponse\Response;
use App\Http\Responses\GeneralResponse\ResultBuilder;
use App\Repositories\Contracts\CustomerRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class CustomerAuthController extends Controller
{
    protected $customerRepository;
    protected $response;

    public function __construct(CustomerRepositoryInterface $customerRepository, Response $response)
    {
        $this->customerRepository = $customerRepository;
        $this->response = $response;
    }

    /**
     * Customer Registration
     */
    public function register(Request $request)
    {
        try {
            // Rate limiting - max 5 attempts per hour
            $key = 'customer-register:' . $request->ip();
            if (RateLimiter::tooManyAttempts($key, 5)) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('429')
                    ->setMessage('Too many registration attempts. Please try again later.')
                    ->setData([]);

                return response()->json($this->response->generateResponse($result), 429);
            }

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:customers,email',
                'phone' => 'nullable|string|max:20|unique:customers,phone',
                'password' => 'required|string|min:8|confirmed',
                'date_of_birth' => 'nullable|date|before:today',
                'gender' => 'nullable|in:male,female,other',
            ]);

            if ($validator->fails()) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('422')
                    ->setMessage('Validation failed')
                    ->setData(['errors' => $validator->errors()]);

                return response()->json($this->response->generateResponse($result), 422);
            }

            RateLimiter::hit($key);

            // Create customer
            $customer = $this->customerRepository->create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => $request->password,
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'status' => 'active',
            ]);

            // Send verification email
            $this->sendVerificationEmail($customer);

            // Create token
            $deviceName = $request->device_name ?? $request->userAgent() ?? 'web';
            $token = $this->customerRepository->createToken($customer->id, $deviceName);

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('201')
                ->setMessage('Registration successful. Please verify your email.')
                ->setData([
                    'token' => $token,
                    'token_type' => 'Bearer',
                    'customer' => [
                        'id' => $customer->id,
                        'customer_code' => $customer->customer_code,
                        'name' => $customer->name,
                        'email' => $customer->email,
                        'phone' => $customer->phone,
                        'email_verified_at' => $customer->email_verified_at,
                        'status' => $customer->status,
                    ]
                ]);

            return response()->json($this->response->generateResponse($result), 201);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Registration failed: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Customer Login
     */
    public function login(Request $request)
    {
        try {
            // Rate limiting - max 10 attempts per minute
            $key = 'customer-login:' . $request->ip();
            if (RateLimiter::tooManyAttempts($key, 10)) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('429')
                    ->setMessage('Too many login attempts. Please try again later.')
                    ->setData([]);

                return response()->json($this->response->generateResponse($result), 429);
            }

            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|string',
            ]);

            if ($validator->fails()) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('422')
                    ->setMessage('Validation failed')
                    ->setData(['errors' => $validator->errors()]);

                return response()->json($this->response->generateResponse($result), 422);
            }

            RateLimiter::hit($key);

            // Get customer by email
            $customer = $this->customerRepository->findByEmail($request->email);

            // Check if customer exists and password is correct
            if (!$customer || !Hash::check($request->password, $customer->password)) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('401')
                    ->setMessage('Invalid credentials.')
                    ->setData([]);

                return response()->json($this->response->generateResponse($result), 401);
            }

            // Check if customer is active
            if ($customer->status !== 'active') {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('403')
                    ->setMessage('Your account has been ' . $customer->status . '.')
                    ->setData([]);

                return response()->json($this->response->generateResponse($result), 403);
            }

            // Clear rate limiter on successful login
            RateLimiter::clear($key);

            // Update last login
            $this->customerRepository->updateLastLogin($customer->id, $request->ip());

            // Create token
            $deviceName = $request->device_name ?? $request->userAgent() ?? 'web';
            $token = $this->customerRepository->createToken($customer->id, $deviceName);

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Login successful.')
                ->setData([
                    'token' => $token,
                    'token_type' => 'Bearer',
                    'customer' => [
                        'id' => $customer->id,
                        'customer_code' => $customer->customer_code,
                        'name' => $customer->name,
                        'email' => $customer->email,
                        'phone' => $customer->phone,
                        'date_of_birth' => $customer->date_of_birth,
                        'gender' => $customer->gender,
                        'segment' => $customer->segment,
                        'is_vip' => $customer->is_vip,
                        'loyalty_points' => $customer->loyalty_points,
                        'total_orders' => $customer->total_orders,
                        'total_spent' => $customer->total_spent,
                        'email_verified_at' => $customer->email_verified_at,
                        'status' => $customer->status,
                    ]
                ]);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Login failed: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Logout (revoke current token)
     */
    public function logout(Request $request)
    {
        try {
            $token = $request->bearerToken();

            if ($token) {
                $this->customerRepository->revokeToken($token);
            }

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Logout successful.')
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Logout failed: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Logout from all devices (revoke all tokens)
     */
    public function logoutAll(Request $request)
    {
        try {
            $customer = $request->customer;

            $this->customerRepository->revokeAllCustomerTokens($customer->id);

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Logged out from all devices successfully.')
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Logout failed: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Get current customer
     */
    public function me(Request $request)
    {
        try {
            $customer = $request->customer;

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Customer data retrieved successfully.')
                ->setData([
                    'id' => $customer->id,
                    'customer_code' => $customer->customer_code,
                    'name' => $customer->name,
                    'email' => $customer->email,
                    'phone' => $customer->phone,
                    'date_of_birth' => $customer->date_of_birth,
                    'gender' => $customer->gender,
                    'segment' => $customer->segment,
                    'is_vip' => $customer->is_vip,
                    'loyalty_points' => $customer->loyalty_points,
                    'total_orders' => $customer->total_orders,
                    'total_spent' => $customer->total_spent,
                    'average_order_value' => $customer->average_order_value,
                    'last_order_at' => $customer->last_order_at,
                    'last_login_at' => $customer->last_login_at,
                    'email_verified_at' => $customer->email_verified_at,
                    'status' => $customer->status,
                ]);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to retrieve customer data: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Get all active sessions/tokens
     */
    public function sessions(Request $request)
    {
        try {
            $customer = $request->customer;

            $tokens = $this->customerRepository->getCustomerTokens($customer->id);

            $sessions = [];
            foreach ($tokens as $token) {
                $sessions[] = [
                    'id' => $token->id,
                    'device_name' => $token->name,
                    'last_used_at' => $token->last_used_at,
                    'created_at' => $token->created_at,
                ];
            }

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Sessions retrieved successfully.')
                ->setData([
                    'sessions' => $sessions,
                    'total' => count($sessions),
                ]);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to retrieve sessions: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Refresh token
     */
    public function refreshToken(Request $request)
    {
        try {
            $customer = $request->customer;
            $oldToken = $request->bearerToken();

            // Revoke old token
            if ($oldToken) {
                $this->customerRepository->revokeToken($oldToken);
            }

            // Create new token
            $deviceName = $request->device_name ?? $request->userAgent() ?? 'web';
            $newToken = $this->customerRepository->createToken($customer->id, $deviceName);

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Token refreshed successfully.')
                ->setData([
                    'token' => $newToken,
                    'token_type' => 'Bearer',
                ]);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to refresh token: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Verify Email
     */
    public function verifyEmail(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'code' => 'required|string',
            ]);

            if ($validator->fails()) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('422')
                    ->setMessage('Validation failed')
                    ->setData(['errors' => $validator->errors()]);

                return response()->json($this->response->generateResponse($result), 422);
            }

            $customer = $this->customerRepository->findByEmail($request->email);

            if (!$customer) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Customer not found.')
                    ->setData([]);

                return response()->json($this->response->generateResponse($result), 404);
            }

            // Verify OTP
            if (!$this->customerRepository->verifyOtp($customer->id, $request->code, 'email_verification')) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('400')
                    ->setMessage('Invalid or expired verification code.')
                    ->setData([]);

                return response()->json($this->response->generateResponse($result), 400);
            }

            // Mark email as verified
            $this->customerRepository->markEmailAsVerified($customer->id);

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Email verified successfully.')
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Email verification failed: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Resend Verification Email
     */
    public function resendVerification(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
            ]);

            if ($validator->fails()) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('422')
                    ->setMessage('Validation failed')
                    ->setData(['errors' => $validator->errors()]);

                return response()->json($this->response->generateResponse($result), 422);
            }

            $customer = $this->customerRepository->findByEmail($request->email);

            if (!$customer) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Customer not found.')
                    ->setData([]);

                return response()->json($this->response->generateResponse($result), 404);
            }

            if ($customer->email_verified_at) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('400')
                    ->setMessage('Email already verified.')
                    ->setData([]);

                return response()->json($this->response->generateResponse($result), 400);
            }

            $this->sendVerificationEmail($customer);

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Verification email sent successfully.')
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to send verification email: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Forgot Password
     */
    public function forgotPassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
            ]);

            if ($validator->fails()) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('422')
                    ->setMessage('Validation failed')
                    ->setData(['errors' => $validator->errors()]);

                return response()->json($this->response->generateResponse($result), 422);
            }

            $customer = $this->customerRepository->findByEmail($request->email);

            if (!$customer) {
                // Don't reveal if email exists or not for security
                $result = (new ResultBuilder())
                    ->setStatus(true)
                    ->setStatusCode('200')
                    ->setMessage('If your email is registered, you will receive a password reset code.')
                    ->setData([]);

                return response()->json($this->response->generateResponse($result), 200);
            }

            // Generate OTP
            $otp = rand(100000, 999999);
            $this->customerRepository->storeOtp($customer->id, $otp, 'password_reset');

            // Send reset email
            $this->sendPasswordResetEmail($customer, $otp);

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Password reset code sent to your email.')
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to process request: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Reset Password
     */
    public function resetPassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'code' => 'required|string',
                'password' => 'required|string|min:8|confirmed',
            ]);

            if ($validator->fails()) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('422')
                    ->setMessage('Validation failed')
                    ->setData(['errors' => $validator->errors()]);

                return response()->json($this->response->generateResponse($result), 422);
            }

            $customer = $this->customerRepository->findByEmail($request->email);

            if (!$customer) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Customer not found.')
                    ->setData([]);

                return response()->json($this->response->generateResponse($result), 404);
            }

            // Verify OTP
            if (!$this->customerRepository->verifyOtp($customer->id, $request->code, 'password_reset')) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('400')
                    ->setMessage('Invalid or expired reset code.')
                    ->setData([]);

                return response()->json($this->response->generateResponse($result), 400);
            }

            // Update password
            $this->customerRepository->update($customer->id, [
                'password' => $request->password,
            ]);

            // Revoke all tokens for security
            $this->customerRepository->revokeAllCustomerTokens($customer->id);

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Password reset successfully. Please login with your new password.')
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Password reset failed: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Send OTP
     */
    public function sendOtp(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'type' => 'required|in:email_verification,phone_verification,password_reset',
            ]);

            if ($validator->fails()) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('422')
                    ->setMessage('Validation failed')
                    ->setData(['errors' => $validator->errors()]);

                return response()->json($this->response->generateResponse($result), 422);
            }

            $customer = $this->customerRepository->findByEmail($request->email);

            if (!$customer) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Customer not found.')
                    ->setData([]);

                return response()->json($this->response->generateResponse($result), 404);
            }

            // Generate OTP
            $otp = rand(100000, 999999);
            $this->customerRepository->storeOtp($customer->id, $otp, $request->type);

            // Send OTP via email
            $this->sendOtpEmail($customer, $otp, $request->type);

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('OTP sent successfully.')
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to send OTP: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Verify OTP
     */
    public function verifyOtp(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'code' => 'required|string',
                'type' => 'required|in:email_verification,phone_verification,password_reset',
            ]);

            if ($validator->fails()) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('422')
                    ->setMessage('Validation failed')
                    ->setData(['errors' => $validator->errors()]);

                return response()->json($this->response->generateResponse($result), 422);
            }

            $customer = $this->customerRepository->findByEmail($request->email);

            if (!$customer) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Customer not found.')
                    ->setData([]);

                return response()->json($this->response->generateResponse($result), 404);
            }

            // Verify OTP
            if (!$this->customerRepository->verifyOtp($customer->id, $request->code, $request->type)) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('400')
                    ->setMessage('Invalid or expired OTP.')
                    ->setData([]);

                return response()->json($this->response->generateResponse($result), 400);
            }

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('OTP verified successfully.')
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('OTP verification failed: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Login with Google (placeholder)
     */
    public function loginWithGoogle(Request $request)
    {
        $result = (new ResultBuilder())
            ->setStatus(false)
            ->setStatusCode('501')
            ->setMessage('Google login not implemented yet.')
            ->setData([]);

        return response()->json($this->response->generateResponse($result), 501);
    }

    /**
     * Login with Facebook (placeholder)
     */
    public function loginWithFacebook(Request $request)
    {
        $result = (new ResultBuilder())
            ->setStatus(false)
            ->setStatusCode('501')
            ->setMessage('Facebook login not implemented yet.')
            ->setData([]);

        return response()->json($this->response->generateResponse($result), 501);
    }

    // Helper Methods

    private function sendVerificationEmail($customer)
    {
        $otp = rand(100000, 999999);
        $this->customerRepository->storeOtp($customer->id, $otp, 'email_verification');

        // TODO: Send actual email using Mail facade
        // For now, just log it
        \Log::info('Verification email for ' . $customer->email . ' - Code: ' . $otp);
    }

    private function sendPasswordResetEmail($customer, $otp)
    {
        // TODO: Send actual email using Mail facade
        // For now, just log it
        \Log::info('Password reset email for ' . $customer->email . ' - Code: ' . $otp);
    }

    private function sendOtpEmail($customer, $otp, $type)
    {
        // TODO: Send actual email using Mail facade
        // For now, just log it
        \Log::info('OTP email for ' . $customer->email . ' - Type: ' . $type . ' - Code: ' . $otp);
    }
}
