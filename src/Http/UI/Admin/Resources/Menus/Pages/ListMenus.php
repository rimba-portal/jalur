<?php

declare(strict_types=1);

namespace Rimba\Menu\Http\UI\Admin\Resources\Menus\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Rimba\Menu\Enums\MenuCategory;
use Rimba\Menu\Http\UI\Admin\Resources\Menus\MenuResource;

class ListMenus extends ListRecords
{
    protected static string $resource = MenuResource::class;

    protected static ?string $title = 'Menu';

    protected ?string $subheading = 'Catalog of all company links.';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        $tabs = [
            'all' => Tab::make('All'),
        ];

        foreach (MenuCategory::cases() as $category) {
            $tabs[$category->value] = Tab::make($category->label())
                ->icon($category->icon())
                ->modifyQueryUsing(fn ($query) => $query->whereRaw(
                    'LOWER(category) = ?',
                    [strtolower($category->value)]
                ));
        }

        return $tabs;
    }
}
