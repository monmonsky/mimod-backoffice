<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanupExpiredTokens extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tokens:cleanup {--days=30 : Delete tokens unused for this many days}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up expired and old personal access tokens';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = $this->option('days');

        // Delete expired tokens
        $expiredCount = DB::table('personal_access_tokens')
            ->where('expires_at', '<', now())
            ->delete();

        $this->info("Deleted {$expiredCount} expired tokens");

        // Delete tokens unused for X days
        $unusedCount = DB::table('personal_access_tokens')
            ->where('last_used_at', '<', now()->subDays($days))
            ->orWhere(function ($query) use ($days) {
                $query->whereNull('last_used_at')
                      ->where('created_at', '<', now()->subDays($days));
            })
            ->delete();

        $this->info("Deleted {$unusedCount} unused tokens (>{$days} days)");

        // Show remaining tokens
        $remaining = DB::table('personal_access_tokens')->count();
        $this->info("Remaining active tokens: {$remaining}");

        // Vacuum table to reclaim space (PostgreSQL)
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('VACUUM ANALYZE personal_access_tokens');
            $this->info('Table vacuumed and analyzed');
        }

        return Command::SUCCESS;
    }
}
