<?php

declare(strict_types=1);

namespace Rimba\Menu\Http\UI\Staff\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Collection;
use Rimba\Menu\Silos\SystemRepository;
use UnitEnum;

class SystemsPage extends Page implements HasTable
{
    use InteractsWithTable;

    protected string $view = 'bites::view-systems';

    protected static ?string $slug = 'systems.apps';

    protected static ?string $title = 'Application Systems';

    protected static ?string $navigationLabel = 'Application';

    protected static ?int $navigationSort = 10;

    protected static string|UnitEnum|null $navigationGroup = 'Systems';

    protected static string|BackedEnum|null $navigationIcon = 'bites-softwares';

    protected ?string $subheading =
        'Available application systems discovered from released versions.';

    public function table(Table $table): Table
    {
        return $table
            ->records(fn (): Collection => collect(app(SystemRepository::class)->active()))
            ->recordUrl(fn (array $record): ?string => $record['url_origin'] ?? null)
            ->openRecordUrlInNewTab()
            ->defaultGroup('name')
            ->columns([
                // TextColumn::make('name')
                //     ->searchable(),
                TextColumn::make('description')
                    ->wrap(),
                TextColumn::make('url_origin')
                    ->label('System'),

            ]);
    }
}
