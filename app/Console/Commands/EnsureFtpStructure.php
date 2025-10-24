<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class EnsureFtpStructure extends Command
{
    protected $signature = 'ftp:ensure-structure';
    protected $description = 'Ensure FTP directory structure exists';

    public function handle()
    {
        $disk = Storage::disk('ftp');

        $directories = [
            'temp',
            'temp/products',
            'temp/variants',
            'products',
            'brands',
            'categories',
            'users',
            'avatars',
            'banners',
        ];

        $this->info('Ensuring FTP directory structure...');

        foreach ($directories as $dir) {
            if (!$disk->exists($dir)) {
                $disk->makeDirectory($dir);
                $this->info("✓ Created: {$dir}");
            } else {
                $this->comment("  Already exists: {$dir}");
            }
        }

        $this->info('FTP structure is ready!');

        return 0;
    }
}
