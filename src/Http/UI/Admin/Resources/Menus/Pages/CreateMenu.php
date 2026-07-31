<?php

declare(strict_types=1);

namespace Rimba\Menu\Http\UI\Admin\Resources\Menus\Pages;

use Filament\Resources\Pages\CreateRecord;
use Rimba\Menu\Http\UI\Admin\Resources\Menus\MenuResource;

class CreateMenu extends CreateRecord
{
    protected static string $resource = MenuResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
