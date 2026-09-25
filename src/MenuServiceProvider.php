<?php

declare(strict_types=1);

namespace Rimba\Menu;

use Illuminate\Support\Facades\File;
use Rimba\Base\Services\BitesServiceProvider;

class MenuServiceProvider extends BitesServiceProvider
{
    protected string $iconsPath = __DIR__.'/../resources/svg';

    protected function bootPackage(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->publishes([__DIR__.'/../setup' => storage_path('setup')], 'menu-setup');
        $this->ensureSetupFilesExist();

    }

    protected function registerPackage(): void
    {
        //
    }

    public static function jsonPath(string $store): string
    {
        return storage_path("setup/json/{$store}.json");
    }

    protected function ensureSetupFilesExist(): void
    {
        $source = __DIR__.'/../setup';
        $destination = storage_path('setup');
        File::ensureDirectoryExists($destination);
        foreach (File::allFiles($source) as $file) {
            $relativePath = $file->getRelativePathname();
            $target = $destination.'/'.$relativePath;
            if (! File::exists($target)) {
                File::ensureDirectoryExists(dirname($target));
                File::copy($file->getRealPath(), $target);
            }
        }
    }
}
