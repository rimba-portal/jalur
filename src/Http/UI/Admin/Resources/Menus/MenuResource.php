<?php

declare(strict_types=1);

namespace Rimba\Menu\Http\UI\Admin\Resources\Menus;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Rimba\Menu\Http\UI\Admin\Resources\Menus\Pages\CreateMenu;
use Rimba\Menu\Http\UI\Admin\Resources\Menus\Pages\EditMenu;
use Rimba\Menu\Http\UI\Admin\Resources\Menus\Pages\ListMenus;
use Rimba\Menu\Http\UI\Admin\Resources\Menus\Pages\ViewMenu;
use Rimba\Menu\Http\UI\Admin\Resources\Menus\Schemas\MenuForm;
use Rimba\Menu\Http\UI\Admin\Resources\Menus\Schemas\MenuInfolist;
use Rimba\Menu\Http\UI\Admin\Resources\Menus\Tables\MenusTable;
use Rimba\Menu\Models\Menu;
use Rimba\Versioning\Http\UI\Admin\Resources\Versions\RelationManagers\VersionsRelationManager;

class MenuResource extends Resource
{
    use ResourceHasVersionRelations;

    protected static ?string $model = Menu::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return MenuForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return MenuInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MenusTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            VersionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMenus::route('/'),
            'create' => CreateMenu::route('/create'),
            'view' => ViewMenu::route('/{record}'),
            'edit' => EditMenu::route('/{record}/edit'),
        ];
    }
}
