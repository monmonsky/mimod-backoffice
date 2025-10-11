<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class CleanupTempImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'images:cleanup-temp {--hours=24 : Delete temp files older than X hours}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cleanup temporary uploaded images that are older than specified hours';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $hours = $this->option('hours');
        $this->info("Cleaning up temporary images older than {$hours} hours...");

        $deletedCount = 0;
        $deletedSize = 0;

        // Get all directories in temp
        $tempDirs = [
            'temp/products',
            'temp/variants',
        ];

        foreach ($tempDirs as $tempDir) {
            if (!Storage::disk('public')->exists($tempDir)) {
                continue;
            }

            // Get all session directories
            $sessionDirs = Storage::disk('public')->directories($tempDir);

            foreach ($sessionDirs as $sessionDir) {
                // Get all files in session directory
                $files = Storage::disk('public')->allFiles($sessionDir);

                foreach ($files as $file) {
                    // Get file last modified time
                    $lastModified = Storage::disk('public')->lastModified($file);
                    $fileAge = Carbon::createFromTimestamp($lastModified);

                    // Check if file is older than specified hours
                    if ($fileAge->diffInHours(now()) >= $hours) {
                        // Get file size before deletion
                        $size = Storage::disk('public')->size($file);
                        $deletedSize += $size;

                        // Delete the file
                        Storage::disk('public')->delete($file);
                        $deletedCount++;

                        $this->line("Deleted: {$file}");
                    }
                }

                // Check if directory is now empty and delete it
                $remainingFiles = Storage::disk('public')->files($sessionDir);
                if (empty($remainingFiles)) {
                    Storage::disk('public')->deleteDirectory($sessionDir);
                    $this->line("Removed empty directory: {$sessionDir}");
                }
            }
        }

        // Convert size to human readable format
        $sizeInMB = round($deletedSize / 1024 / 1024, 2);

        $this->info("Cleanup completed!");
        $this->info("Deleted files: {$deletedCount}");
        $this->info("Freed space: {$sizeInMB} MB");

        return Command::SUCCESS;
    }
}
