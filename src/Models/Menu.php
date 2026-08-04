<?php

declare(strict_types=1);

namespace Rimba\Menu\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Rimba\Versioning\Traits\HasVersions;

#[Fillable([
    'category',
    'group',
    'name',
    'slug',
    'description',
    'icon',
    'color',
    'parent_id',
    'permission',
    'panel',

    'is_visible',
    'is_active',
    'sort',
])]
class Menu extends Model
{
    // use HasAttachableExtLink;
    use HasVersions;

    protected $guard_name = 'web';

    protected $attributes = [
        'is_active' => false,
    ];

    public static function seedMappings(): array
    {
        return [

            'category' => fn ($value): array => [
                'category' => str($value)
                    ->slug()
                    ->value(),
            ],

            'group' => fn ($value): array => [
                'group' => str($value)
                    ->slug()
                    ->value(),
            ],

        ];
    }
}
