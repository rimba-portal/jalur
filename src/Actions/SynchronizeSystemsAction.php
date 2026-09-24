<?php

declare(strict_types=1);

namespace Rimba\Menu\Actions;

use Illuminate\Support\Str;
use JsonException;
use Rimba\Menu\Silos\SystemRepository;

class SynchronizeSystemsAction
{
    public function __construct(
        private readonly ListPublishedUrlOriginsAction $listPublishedUrlOriginsAction,
        private readonly SystemRepository $systemRepository,
    ) {}

    /**
     * @param  array<class-string>  $versionableTypes
     *
     * @throws JsonException
     */
    public function handle(
        array $versionableTypes
    ): array {

        $systems = $this->systemRepository->all();

        $discovered = collect(
            $this->listPublishedUrlOriginsAction->handle(
                $versionableTypes
            )
        )
            ->groupBy('url_origin')
            ->map(function ($items, string $origin): array {

                return [
                    'url_origin' => $origin,

                    'name' => $items->first()['name'],

                    'description' => $items->first()['description'],

                    'count' => $items->sum('count'),

                    'versionable_types' => $items
                        ->pluck('versionable_type')
                        ->unique()
                        ->sort()
                        ->values()
                        ->all(),
                ];
            });

        $existingByOrigin = collect($systems)
            ->filter(
                fn (array $system): bool => filled($system['url_origin'] ?? null)
            )
            ->keyBy(
                fn (array $system): string => $this->normalizeOrigin(
                    $system['url_origin']
                )
            );

        $merged = $existingByOrigin
            ->map(function (
                array $system,
                string $origin
            ) use ($discovered): array {

                $found = $discovered->get($origin);

                return array_replace(
                    $system,
                    [
                        'url_origin' => $origin,
                        'name' => $found['name'] ?? $system['name'] ?? null,
                        'description' => $found['description'] ?? $system['description'] ?? null,
                        'active' => $found !== null,
                        'count' => $found['count'] ?? 0,
                        'versionable_types' => $found['versionable_types'] ?? [],
                    ]
                );
            });

        foreach ($discovered as $origin => $item) {

            if ($merged->has($origin)) {
                continue;
            }

            $merged->put(
                $origin,
                [
                    'name' => $item['name'] ?: $this->makeName($origin),

                    'slug' => Str::slug(
                        parse_url(
                            $origin,
                            PHP_URL_HOST
                        ) ?: $origin
                    ),

                    'url_origin' => $origin,

                    'description' => $item['description'],

                    'icon' => null,

                    'active' => true,

                    'count' => $item['count'],

                    'versionable_types' => $item['versionable_types'],
                ]
            );
        }

        $result = $merged
            ->sortBy('name')
            ->values()
            ->all();

        $this->systemRepository->save($result);

        return $result;
    }

    private function normalizeOrigin(
        string $origin
    ): string {
        return rtrim(
            strtolower($origin),
            '/'
        );
    }

    private function makeName(
        string $origin
    ): string {

        $host = parse_url(
            $origin,
            PHP_URL_HOST
        );

        return Str::of(
            $host ?: $origin
        )
            ->replace('www.', '')
            ->before('.')
            ->replace(
                ['-', '_'],
                ' '
            )
            ->title()
            ->value();
    }
}
