<?php

namespace Rimba\Menu\Http\UI\Admin\Resources\Menus;

use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class MenuResource extends Resource
{
    protected static ?string $model = \Rimba\Menu\Models\Menu::class;

    protected static string|UnitEnum|null $navigationGroup = 'Menu';

    protected static string|BackedEnum|null $navigationIcon = 'bites-s-menu';

    protected static ?int $navigationSort = 27;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema { return \Rimba\Menu\Http\UI\Admin\Resources\Menus\Schemas\MenuForm::configure($schema); }

    public static function infolist(Schema $schema): Schema { return \Rimba\Menu\Http\UI\Admin\Resources\Menus\Schemas\MenuInfolist::configure($schema); }

    public static function table(Table $table): Table { return \Rimba\Menu\Http\UI\Admin\Resources\Menus\Tables\MenusTable::configure($table); }

    public static function getRelations(): array 
    { 
        return [ 
            \Rimba\Versioning\Http\UI\Admin\Resources\Versions\RelationManagers\VersionsRelationManager::class, 
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => \Rimba\Menu\Http\UI\Admin\Resources\Menus\Pages\ListMenus::route('/'),
             'create' => \Rimba\Menu\Http\UI\Admin\Resources\Menus\Pages\CreateMenu::route('/create'),
             'view' => \Rimba\Menu\Http\UI\Admin\Resources\Menus\Pages\ViewMenu::route('/{record}'),
             'edit' => \Rimba\Menu\Http\UI\Admin\Resources\Menus\Pages\EditMenu::route('/{record}/edit'),
            //
        ];
    }
}
