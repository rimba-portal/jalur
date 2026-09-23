<?php

declare(strict_types=1);

namespace Rimba\Menu\Http\UI\Staff\Pages;

use BackedEnum;
use Rimba\Base\Pages\JsonTablePage;
use Rimba\Menu\MenuServiceProvider;
use UnitEnum;

class ToolsPage extends JsonTablePage
{
    protected static string $store = 'terminologies';

    protected static ?string $title = 'Terminologies';

    protected static string|UnitEnum|null $navigationGroup = 'Knowledge';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-wrench-screwdriver';

    protected ?string $subheading = 'Various philosophies, methodologies and utilities for staff to use in their daily tasks.';

    protected static ?string $navigationLabel = 'Terminology';

    protected static ?int $navigationSort = 22;

    protected static function sourcePath(): string
    {
        return MenuServiceProvider::jsonPath(
            static::$store
        );
    }
}
