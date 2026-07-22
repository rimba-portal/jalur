<?php

declare(strict_types=1);

namespace Rimba\Menu\Services;

use Illuminate\Http\RedirectResponse;
use Rimba\Menu\Models\Menu;
use Rimba\Versioning\Enums\ContentType;
use Rimba\Versioning\Models\Version;

class MenuResolver
{
    public function resolve(Menu $menu): RedirectResponse|string|null
    {
        /** @var Version|null $version */
        $version = $menu->activeVersion;

        if (! $version) {
            return null;
        }

        return $this->resolveVersion($version);
    }

    public function resolveVersion(Version $version): RedirectResponse|string|null
    {
        return match (ContentType::from($version->content_type)) {

            ContentType::Route => redirect()->route($version->target),

            ContentType::Url => redirect()->away($version->target),

            ContentType::FilamentPage => redirect()->route($version->target),

            ContentType::FilamentResource => redirect()->route($version->target),

            ContentType::Dashboard => redirect()->route($version->target),

            ContentType::Report => redirect()->route($version->target),

            ContentType::Document,
            ContentType::Folder,
            ContentType::Markdown,
            ContentType::File,
            ContentType::Api,
            ContentType::Video,
            ContentType::Html => $version->target,

        };
    }
}
