<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\PersonalAccessToken;

class GenerateStoreToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'store:generate-token
                            {--name=Store Frontend : Token name/description}
                            {--revoke-existing : Revoke all existing tokens for store user}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a lifetime API token for store frontend access';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Generating Store API Token...');
        $this->newLine();

        // Get store API user
        $user = DB::table('users')->where('email', 'store@mimod.com')->first();

        if (!$user) {
            $this->error('Store API user not found!');
            $this->info('Please create the store API user first.');
            return 1;
        }

        // Revoke existing tokens if requested
        if ($this->option('revoke-existing')) {
            $revokedCount = DB::table('personal_access_tokens')
                ->where('tokenable_type', 'App\Models\User')
                ->where('tokenable_id', $user->id)
                ->delete();

            if ($revokedCount > 0) {
                $this->warn("Revoked {$revokedCount} existing token(s)");
                $this->newLine();
            }
        }

        // Get token name
        $tokenName = $this->option('name');

        // Define token abilities (read-only permissions)
        $abilities = [
            'store:read',
            'products:read',
            'categories:read',
            'brands:read',
            'settings:read',
        ];

        // Generate token
        $plainTextToken = $this->generateToken($user->id, $tokenName, $abilities);

        // Display results
        $this->displayTokenInfo($user, $tokenName, $plainTextToken, $abilities);

        return 0;
    }

    /**
     * Generate lifetime token for user
     */
    private function generateToken($userId, $tokenName, $abilities)
    {
        // Generate random token (64 characters)
        $token = bin2hex(random_bytes(32));

        // Hash token for storage
        $hashedToken = hash('sha256', $token);

        // Insert token to database (without expires_at for lifetime token)
        DB::table('personal_access_tokens')->insert([
            'tokenable_type' => 'App\Models\User',
            'tokenable_id' => $userId,
            'name' => $tokenName,
            'token' => $hashedToken,
            'abilities' => json_encode($abilities),
            'expires_at' => null, // NULL = lifetime token
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Get token ID
        $tokenId = DB::table('personal_access_tokens')
            ->where('token', $hashedToken)
            ->value('id');

        // Return plain text token with ID prefix (Sanctum format)
        return $tokenId . '|' . $token;
    }

    /**
     * Display token information
     */
    private function displayTokenInfo($user, $tokenName, $plainTextToken, $abilities)
    {
        $this->info('✓ Token generated successfully!');
        $this->newLine();

        // Display user info
        $this->line('<fg=cyan>User Information:</>');
        $this->line("  Name  : {$user->name}");
        $this->line("  Email : {$user->email}");
        $this->line("  ID    : {$user->id}");
        $this->newLine();

        // Display token info
        $this->line('<fg=cyan>Token Information:</>');
        $this->line("  Name     : {$tokenName}");
        $this->line("  Type     : Lifetime (No Expiration)");
        $this->line("  Created  : " . now()->format('Y-m-d H:i:s'));
        $this->newLine();

        // Display abilities
        $this->line('<fg=cyan>Token Abilities (Permissions):</>');
        foreach ($abilities as $ability) {
            $this->line("  • {$ability}");
        }
        $this->newLine();

        // Display token (highlight it)
        $this->line('<fg=yellow;options=bold>═══════════════════════════════════════════════════════════════════</>');
        $this->line('<fg=yellow;options=bold>                        YOUR API TOKEN                              </>');
        $this->line('<fg=yellow;options=bold>═══════════════════════════════════════════════════════════════════</>');
        $this->newLine();
        $this->line('<fg=green;options=bold>' . $plainTextToken . '</>');
        $this->newLine();
        $this->line('<fg=yellow;options=bold>═══════════════════════════════════════════════════════════════════</>');
        $this->newLine();

        // Display warning
        $this->warn('⚠️  IMPORTANT SECURITY NOTES:');
        $this->line('  1. Save this token securely - it will NOT be shown again');
        $this->line('  2. Add it to your .env file: STORE_API_TOKEN=' . $plainTextToken);
        $this->line('  3. Never commit this token to version control');
        $this->line('  4. This token has lifetime access (no expiration)');
        $this->line('  5. Revoke immediately if compromised');
        $this->newLine();

        // Display usage example
        $this->line('<fg=cyan>Usage Example:</>');
        $this->line('  curl -H "Authorization: Bearer ' . substr($plainTextToken, 0, 30) . '..." \\');
        $this->line('       http://127.0.0.1:8000/api/store/products');
        $this->newLine();
    }
}
