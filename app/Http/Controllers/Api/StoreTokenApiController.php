<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\GeneralResponse\Response;
use App\Http\Responses\GeneralResponse\ResultBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\PersonalAccessToken;

class StoreTokenApiController extends Controller
{
    protected $response;

    public function __construct(Response $response)
    {
        $this->response = $response;
    }

    /**
     * Get all store API tokens
     */
    public function index()
    {
        try {
            // Get store API user
            $storeUser = DB::table('users')->where('email', 'store@mimod.com')->first();

            if (!$storeUser) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Store API user not found');

                return response()->json($this->response->generateResponse($result), 404);
            }

            // Get all tokens for store user
            $tokens = DB::table('personal_access_tokens')
                ->where('tokenable_type', 'App\Models\User')
                ->where('tokenable_id', $storeUser->id)
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($token) {
                    return [
                        'id' => $token->id,
                        'name' => $token->name,
                        'abilities' => json_decode($token->abilities, true),
                        'is_lifetime' => $token->expires_at === null,
                        'expires_at' => $token->expires_at,
                        'last_used_at' => $token->last_used_at,
                        'created_at' => $token->created_at,
                    ];
                });

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Store tokens retrieved successfully')
                ->setData([
                    'tokens' => $tokens,
                    'total' => $tokens->count(),
                ]);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to retrieve tokens: ' . $e->getMessage());

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Get single token details
     */
    public function show($id)
    {
        try {
            $token = DB::table('personal_access_tokens')
                ->where('id', $id)
                ->first();

            if (!$token) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Token not found');

                return response()->json($this->response->generateResponse($result), 404);
            }

            $tokenData = [
                'id' => $token->id,
                'name' => $token->name,
                'abilities' => json_decode($token->abilities, true),
                'is_lifetime' => $token->expires_at === null,
                'expires_at' => $token->expires_at,
                'last_used_at' => $token->last_used_at,
                'created_at' => $token->created_at,
            ];

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Token retrieved successfully')
                ->setData($tokenData);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to retrieve token: ' . $e->getMessage());

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Revoke (delete) a store token
     */
    public function destroy($id)
    {
        try {
            $token = DB::table('personal_access_tokens')
                ->where('id', $id)
                ->first();

            if (!$token) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Token not found');

                return response()->json($this->response->generateResponse($result), 404);
            }

            // Delete token
            DB::table('personal_access_tokens')->where('id', $id)->delete();

            // Log activity
            logActivity('delete', "Revoked store API token: {$token->name}", 'store_token', (int) $id);

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Token revoked successfully');

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to revoke token: ' . $e->getMessage());

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Generate new store token
     */
    public function generate(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'revoke_existing' => 'nullable|boolean',
            ]);

            // Get store API user
            $storeUser = DB::table('users')->where('email', 'store@mimod.com')->first();

            if (!$storeUser) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Store API user not found');

                return response()->json($this->response->generateResponse($result), 404);
            }

            // Revoke existing tokens if requested
            if ($request->revoke_existing) {
                $revokedCount = DB::table('personal_access_tokens')
                    ->where('tokenable_type', 'App\Models\User')
                    ->where('tokenable_id', $storeUser->id)
                    ->delete();

                if ($revokedCount > 0) {
                    logActivity('delete', "Revoked {$revokedCount} existing store API token(s)", 'store_token', null);
                }
            }

            // Define token abilities
            $abilities = [
                'store:read',
                'products:read',
                'categories:read',
                'brands:read',
                'settings:read',
            ];

            // Generate token
            $token = bin2hex(random_bytes(32));
            $hashedToken = hash('sha256', $token);

            // Insert token
            $tokenId = DB::table('personal_access_tokens')->insertGetId([
                'tokenable_type' => 'App\Models\User',
                'tokenable_id' => $storeUser->id,
                'name' => $validated['name'],
                'token' => $hashedToken,
                'abilities' => json_encode($abilities),
                'expires_at' => null, // Lifetime token
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $plainTextToken = $tokenId . '|' . $token;

            // Log activity
            logActivity('create', "Generated new store API token: {$validated['name']}", 'store_token', (int) $tokenId);

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('201')
                ->setMessage('Store token generated successfully')
                ->setData([
                    'token_id' => $tokenId,
                    'token' => $plainTextToken,
                    'name' => $validated['name'],
                    'abilities' => $abilities,
                    'is_lifetime' => true,
                    'created_at' => now()->toDateTimeString(),
                    'warning' => 'Save this token securely. It will not be shown again.',
                ]);

            return response()->json($this->response->generateResponse($result), 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('422')
                ->setMessage('Validation failed')
                ->setData(['errors' => $e->errors()]);

            return response()->json($this->response->generateResponse($result), 422);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to generate token: ' . $e->getMessage());

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Get token usage statistics
     */
    public function stats()
    {
        try {
            // Get store API user
            $storeUser = DB::table('users')->where('email', 'store@mimod.com')->first();

            if (!$storeUser) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Store API user not found');

                return response()->json($this->response->generateResponse($result), 404);
            }

            // Get statistics
            $totalTokens = DB::table('personal_access_tokens')
                ->where('tokenable_type', 'App\Models\User')
                ->where('tokenable_id', $storeUser->id)
                ->count();

            $activeTokens = DB::table('personal_access_tokens')
                ->where('tokenable_type', 'App\Models\User')
                ->where('tokenable_id', $storeUser->id)
                ->where(function ($query) {
                    $query->whereNull('expires_at')
                          ->orWhere('expires_at', '>', now());
                })
                ->count();

            $recentlyUsed = DB::table('personal_access_tokens')
                ->where('tokenable_type', 'App\Models\User')
                ->where('tokenable_id', $storeUser->id)
                ->where('last_used_at', '>', now()->subDays(7))
                ->count();

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Token statistics retrieved successfully')
                ->setData([
                    'total_tokens' => $totalTokens,
                    'active_tokens' => $activeTokens,
                    'recently_used' => $recentlyUsed,
                    'last_7_days' => $recentlyUsed,
                ]);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to retrieve statistics: ' . $e->getMessage());

            return response()->json($this->response->generateResponse($result), 500);
        }
    }
}
