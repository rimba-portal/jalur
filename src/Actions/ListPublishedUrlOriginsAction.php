<?php

declare(strict_types=1);

namespace Rimba\Menu\Actions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Rimba\Versioning\Models\Version;

class ListPublishedUrlOriginsAction
{
    /**
     * @param  array<class-string>  $versionableTypes
     */
    public function handle(array $versionableTypes = []): array
    {
        return Version::query()
            ->with('versionable')
            ->where('content_type', 'url')
            ->where('status', '!=', 'draft')
            ->when(
                $versionableTypes !== [],
                fn ($query) => $query->whereIn(
                    'versionable_type',
                    $versionableTypes
                )
            )
            ->get([
                'versionable_id',
                'versionable_type',
                'content_url',
            ])
            ->map(function (Version $version): ?array {
                // dump([
                //     'type' => $version->versionable_type,
                //     'id' => $version->versionable_id,
                //     'loaded' => $version->relationLoaded('versionable'),
                //     'model' => $version->versionable,
                // ]);
                $origin = $this->extractOrigin(
                    $version->content_url
                );

                if ($origin === null) {
                    return null;
                }

                $metadata = $this->extractMetadata(
                    $version->versionable
                );

                // dump($result);

                return [
                    'url_origin' => $origin,
                    'versionable_type' => $version->versionable_type,
                    'name' => $metadata['name'],
                    'description' => $metadata['description'],
                ];
            })
            ->filter()
            ->groupBy(
                fn (array $item): string => $item['url_origin']
                    .'|'
                    .$item['versionable_type']
            )
            ->map(
                fn (Collection $items): array => [

                    'url_origin' => $items->first()['url_origin'],

                    'versionable_type' => $items->first()['versionable_type'],

                    'name' => $items->first()['name'],

                    'description' => $items->first()['description'],

                    'count' => $items->count(),
                ]
            )
            ->sortBy('url_origin')
            ->values()
            ->all();
    }

    private function extractMetadata(
        ?Model $model
    ): array {

        if (! $model instanceof Model) {
            return [
                'name' => null,
                'description' => null,
            ];
        }

        return [

            'name' => $this->attribute(
                $model,
                ['name', 'title', 'label']
            ),

            'description' => $this->attribute(
                $model,
                [
                    'description',
                    'summary',
                    'remarks',
                    'notes',
                ]
            ),

        ];
    }

    private function attribute(
        Model $model,
        array $candidates
    ): ?string {

        foreach ($candidates as $candidate) {

            if (
                array_key_exists(
                    $candidate,
                    $model->getAttributes()
                )
            ) {
                return (string) $model->{$candidate};
            }
        }

        return null;
    }

    private function extractOrigin(
        string $url
    ): ?string {

        $scheme = parse_url(
            $url,
            PHP_URL_SCHEME
        );

        $host = parse_url(
            $url,
            PHP_URL_HOST
        );

        $port = parse_url(
            $url,
            PHP_URL_PORT
        );

        if (
            ! is_string($scheme)
            || ! is_string($host)
        ) {
            return null;
        }

        return sprintf(
            '%s://%s%s',
            strtolower($scheme),
            strtolower($host),
            $port ? ':'.$port : ''
        );
    }
}
