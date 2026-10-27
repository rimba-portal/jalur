<?php

declare(strict_types=1);

namespace Rimba\Menu\Http\UI\Staff\Schemas;

use Illuminate\Support\Facades\Storage;

class EscalationDataLoader
{
    public function load(): array
    {
        $file = Storage::disk('local')->get('feed/escalation.json');

        if (! file_exists($file)) {
            return [];
        }

        return json_decode(
            file_get_contents($file),
            true
        ) ?? [];
    }

    public function get(string $category): array
    {
        return $this->load()[$category] ?? [];
    }
}
