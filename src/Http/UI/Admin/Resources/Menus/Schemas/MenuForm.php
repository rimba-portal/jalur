<?php

declare(strict_types=1);

namespace Rimba\Menu\Http\UI\Admin\Resources\Menus\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Rimba\Menu\Enums\MenuCategory;
use Rimba\Menu\Enums\MenuGroup;

class MenuForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('category')
                    ->options(MenuCategory::options())
                    ->required()
                    ->live(),

                Select::make('group')
                    ->options(function (Get $get): array {

                        $category = $get('category');

                        if (! $category) {
                            return [];
                        }

                        return MenuGroup::optionsForCategory(
                            MenuCategory::from($category)
                        );
                    })
                    ->required(),
                TextInput::make('name')
                    ->required(),
                TextInput::make('slug')
                    ->required(),
                TextInput::make('description'),
                TextInput::make('icon'),
                TextInput::make('color'),
                TextInput::make('parent_id')
                    ->numeric(),
                TextInput::make('permission'),
                TextInput::make('panel'),
                Toggle::make('is_visible')
                    ->required(),
                Toggle::make('is_active')
                    ->required(),
                TextInput::make('sort')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }
}
