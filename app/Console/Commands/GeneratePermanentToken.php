<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GeneratePermanentToken extends Command
{
    protected $signature = 'token:generate {email}';
    protected $description = 'Generate permanent API token for a user';

    public function handle()
    {
        $email = $this->argument('email');

        $user = DB::table('users')->where('email', $email)->first();

        if (!$user) {
            $this->error("User with email {$email} not found");
            return 1;
        }

        // Create new token without expiration
        $plainTextToken = bin2hex(random_bytes(40));
        $hashedToken = hash('sha256', $plainTextToken);

        $tokenId = DB::table('personal_access_tokens')->insertGetId([
            'tokenable_type' => 'App\\Models\\User',
            'tokenable_id' => $user->id,
            'name' => 'api-permanent',
            'token' => $hashedToken,
            'abilities' => '["*"]',
            'expires_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $fullToken = $tokenId . '|' . $plainTextToken;

        $this->info('Permanent token generated successfully!');
        $this->line('');
        $this->line('User: ' . $user->email);
        $this->line('Token ID: ' . $tokenId);
        $this->line('Token: ' . $fullToken);
        $this->line('Expires: Never');
        $this->line('');
        $this->warn('Save this token securely. It will not be shown again.');

        return 0;
    }
}
