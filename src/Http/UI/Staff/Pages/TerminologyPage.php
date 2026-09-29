<?php

declare(strict_types=1);

namespace Rimba\Menu\Http\UI\Staff\Pages;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Collection;
use Rimba\Base\Support\JsonFilesManager;
use Throwable;
use UnitEnum;

class TerminologyPage extends Page implements HasTable
{
    use InteractsWithTable;

    // Track the active tab filter state directly using Livewire
    public string $activeTab = 'all';

    protected static string $store = 'terminologies';

    protected string $view = 'bites::view-terminologies';

    protected static ?string $title = 'Terminologies';

    protected static string|UnitEnum|null $navigationGroup = 'Knowledge';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-wrench-screwdriver';

    protected ?string $subheading = 'Various philosophies, methodologies and utilities for staff to use in their daily tasks.';

    protected static ?string $navigationLabel = 'Terminology';

    protected static ?int $navigationSort = 22;

    public function table(Table $table): Table
    {
        return $table
            // Inject the runtime search, sort, and filter flags cleanly
            ->records(function (?string $sortColumn, ?string $sortDirection, ?string $tableSearch): Collection {
                $records = app(JsonFilesManager::class, ['filename' => static::$store])->all();

                // 1. Filter JSON records if a specific horizontal tab is selected
                if ($this->activeTab !== 'all') {
                    $records = $records->filter(function (array $record): bool {
                        return ($record['category'] ?? '') === $this->activeTab;
                    });
                }

                // 2. Filter records dynamically when typing into the Search box
                if ($tableSearch) {
                    $searchTerm = strtolower($tableSearch);
                    $records = $records->filter(fn ($r): bool => str_contains(strtolower($r['name'] ?? ''), $searchTerm));
                }

                // 3. Handle column header sorting loops manually
                return $records->when(
                    $sortColumn,
                    fn ($c) => $sortDirection === 'desc' ? $c->sortByDesc($sortColumn) : $c->sortBy($sortColumn),
                    fn ($c) => $c->sortBy(['category', 'name'])
                );
            })
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('description')
                    ->wrap(),
                TextColumn::make('more')
                    ->label('More Info'),
            ])
            ->actions([
                Action::make('edit')
                    ->label('Edit')
                    ->icon('heroicon-o-pencil-square')
                    ->color('warning')
                    // $record here is a clean, pure array element from your JSON file!
                    ->fillForm(fn (array $record): array => $record)
                    ->form([
                        TextInput::make('name')->disabled(),
                        Textarea::make('description'),
                        TextInput::make('more')->label('System URL')->required()->url(),
                    ])
                    ->action(function (array $data, array $record): void {
                        try {
                            $manager = app(JsonFilesManager::class, ['filename' => static::$store]);

                            // Pass the key origin from the row array to update the exact match
                            $saved = $manager->updateByOrigin(
                                $record['name'] ?? '',
                                $data
                            );

                            if (! $saved) {
                                throw new \Exception('Could not find the target record to update.');
                            }

                            Notification::make()
                                ->title('Terminology updated successfully')
                                ->success()
                                ->send();
                        } catch (Throwable $throwable) {
                            report($throwable);
                            Notification::make()
                                ->title('Update failed')
                                ->body($throwable->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
            ]);
    }

    /**
     * Helper mapping method to pull dynamic categories and item counts
     */
    public function getCategoriesWithCounts(): Collection
    {
        $records = app(JsonFilesManager::class, ['filename' => static::$store])->all();

        return $records->groupBy('category')->map(fn ($group) => $group->count());
    }
}
