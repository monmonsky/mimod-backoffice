<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApiTokenController extends Controller
{
    /**
     * Display API token management page
     */
    public function index()
    {
        // Get API user
        $apiUser = User::where('name', 'API')->first();

        if (!$apiUser) {
            return redirect()->back()->with('error', 'API user not found. Please run seeder first.');
        }

        // Get all tokens for API user
        $tokens = DB::table('personal_access_tokens')
            ->where('tokenable_type', 'App\Models\User')
            ->where('tokenable_id', $apiUser->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('pages.settings.generals.api-tokens', compact('apiUser', 'tokens'));
    }

    /**
     * Generate new API token
     */
    public function generate(Request $request)
    {
        try {
            $request->validate([
                'token_name' => 'required|string|max:255',
                'abilities' => 'nullable|array'
            ]);

            // Get API user
            $apiUser = User::where('name', 'API')->first();

            if (!$apiUser) {
                return response()->json([
                    'success' => false,
                    'message' => 'API user not found. Please run seeder first.'
                ], 404);
            }

            // Default abilities
            $abilities = $request->abilities ?? ['*'];

            // Generate token
            $tokenName = $request->token_name;
            $token = $apiUser->createToken($tokenName, $abilities);

            return response()->json([
                'success' => true,
                'message' => 'API token generated successfully!',
                'data' => [
                    'token' => $token->plainTextToken,
                    'token_id' => $token->accessToken->id,
                    'token_name' => $tokenName,
                    'abilities' => $abilities,
                    'created_at' => $token->accessToken->created_at->format('Y-m-d H:i:s')
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate token: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Revoke (delete) specific token
     */
    public function revoke($tokenId)
    {
        try {
            $token = DB::table('personal_access_tokens')
                ->where('id', $tokenId)
                ->first();

            if (!$token) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token not found'
                ], 404);
            }

            // Verify it's an API user token
            $apiUser = User::where('name', 'API')->first();
            if ($token->tokenable_id !== $apiUser->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot revoke this token'
                ], 403);
            }

            DB::table('personal_access_tokens')
                ->where('id', $tokenId)
                ->delete();

            return response()->json([
                'success' => true,
                'message' => 'Token revoked successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to revoke token: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Revoke all tokens
     */
    public function revokeAll()
    {
        try {
            $apiUser = User::where('name', 'API')->first();

            if (!$apiUser) {
                return response()->json([
                    'success' => false,
                    'message' => 'API user not found'
                ], 404);
            }

            $count = DB::table('personal_access_tokens')
                ->where('tokenable_type', 'App\Models\User')
                ->where('tokenable_id', $apiUser->id)
                ->delete();

            return response()->json([
                'success' => true,
                'message' => "Revoked {$count} token(s) successfully"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to revoke tokens: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get token details (without plaintext)
     */
    public function show($tokenId)
    {
        try {
            $token = DB::table('personal_access_tokens')
                ->where('id', $tokenId)
                ->first();

            if (!$token) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $token->id,
                    'name' => $token->name,
                    'abilities' => json_decode($token->abilities),
                    'last_used_at' => $token->last_used_at,
                    'expires_at' => $token->expires_at,
                    'created_at' => $token->created_at
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get token details: ' . $e->getMessage()
            ], 500);
        }
    }
}
