<?php

declare(strict_types=1);

namespace Rimba\Menu\Http\UI\Staff\Resources\Menus\Tables;

use Filament\Support\Enums\TextSize;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class MenusTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->has('versions'))
            ->columns([
                Split::make([
                    ImageColumn::make('icon')
                        ->label('')
                        ->circular()
                        ->grow(false)
                        ->defaultImageUrl('https://raw.githubusercontent.com/bit-ecosystem/bites/refs/heads/main/menu/business-idea.svg'), // to chanage to Str::kebab($record->title)
                    Stack::make([
                        TextColumn::make('name')
                            ->label('Name')
                            // ->searchable()
                            ->color('primary'),
                        TextColumn::make('description')
                            ->size(TextSize::ExtraSmall)
                            ->wrap(),
                    ]),
                ]),
            ])
            ->paginated(false)
            ->contentGrid([
                'md' => 2,
                'xl' => 4,
            ])
            ->recordUrl(
                fn (Model $model): string => $model->currentVersion()?->url() ?? '#'
            )
            ->openRecordUrlInNewTab(
                fn (Model $model): bool => $model->currentVersion()?->openInNewTab() ?? false
            )
            ->filters([])
            ->toolbarActions([]);
    }
}
