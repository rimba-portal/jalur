<?php

declare(strict_types=1);

namespace Rimba\Menu;

use Rimba\Base\Services\BitesServiceProvider;

class MenuServiceProvider extends BitesServiceProvider
{
    protected string $iconsPath = __DIR__.'/../resources/svg';

    public static function jsonPath(string $store): string
    {
        return __DIR__."/../setup/json/{$store}.json";
    }

    protected function bootPackage(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->publishes([__DIR__.'/../setup' => storage_path('setup')], 'menu-setup');
    }

    protected function registerPackage(): void
    {
        //
    }
}
