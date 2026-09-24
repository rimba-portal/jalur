<?php

declare(strict_types=1);

namespace Rimba\Menu\Silos;

use Illuminate\Support\Facades\File;
use JsonException;
use Rimba\Menu\MenuServiceProvider;

class SystemRepository
{
    public function path(): string
    {
        return MenuServiceProvider::jsonPath(
            'systems'
        );
    }

    /**
     * @throws JsonException
     */
    public function all(): array
    {
        if (! File::exists($this->path())) {
            return [];
        }

        return json_decode(
            File::get($this->path()),
            true,
            512,
            JSON_THROW_ON_ERROR
        );
    }

    /**
     * @throws JsonException
     */
    public function save(array $systems): void
    {
        File::ensureDirectoryExists(
            dirname($this->path())
        );

        File::put(
            $this->path(),
            json_encode(
                $systems,
                JSON_PRETTY_PRINT
                | JSON_UNESCAPED_SLASHES
                | JSON_THROW_ON_ERROR
            ).PHP_EOL
        );
    }

    public function active(): array
    {
        return collect($this->all())
            ->where('active', true)
            ->values()
            ->all();
    }
}
