<?php

declare(strict_types=1);

namespace Rimba\Menu\Http\UI\Admin\Pages;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Callout;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Collection;
use Rimba\Menu\Actions\ListPublishedUrlOriginsAction;
use Rimba\Menu\Actions\SynchronizeSystemsAction;
use Rimba\Menu\Models\Menu;
use Rimba\Menu\Silos\SystemRepository;
use Throwable;
use UnitEnum;

class ManageSystemsPage extends Page implements HasTable
{
    use InteractsWithTable;

    protected string $view = 'bites::manage-systems';

    protected static string|UnitEnum|null $navigationGroup =
        'Systems';

    protected static string|BackedEnum|null $navigationIcon =
        'heroicon-o-server-stack';

    protected static ?string $navigationLabel =
        'System Discovery';

    protected static ?string $title =
        'System Discovery';

    protected static ?int $navigationSort = 20;

    /**
     * @var array<class-string>
     */
    protected array $versionableTypes = [
        Menu::class,
    ];

    public array $newFoundSystems = [];

    public array $orphanSystems = [];

    public function mount(): void
    {
        $this->refreshData();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('refresh')
                ->label('Refresh')
                ->icon('heroicon-o-arrow-path-rounded-square')
                ->color('gray')
                ->action(
                    fn () => $this->refreshData()
                ),

        ];
    }

    public function synchronizeSystems(): void
    {
        try {

            $systems = app(
                SynchronizeSystemsAction::class
            )->handle(
                $this->versionableTypes
            );

            $this->refreshData();

            Notification::make()
                ->title('Systems synchronized')
                ->body(
                    count($systems)
                        .' systems are now configured.'
                )
                ->success()
                ->send();
        } catch (Throwable $throwable) {

            report($throwable);

            Notification::make()
                ->title('Synchronization failed')
                ->body(
                    $throwable->getMessage()
                )
                ->danger()
                ->send();
        }

        $this->redirect(
            static::getUrl(),
            navigate: true
        );
    }

    public function refreshData(): void
    {
        $discovered = collect(
            app(
                ListPublishedUrlOriginsAction::class
            )->handle(
                $this->versionableTypes
            )
        );

        $configured = collect(
            app(SystemRepository::class)
                ->all()
        );

        $configuredOrigins = $configured
            ->pluck('url_origin')
            ->filter()
            ->map(
                fn (string $origin): string => $this->normalizeOrigin($origin)
            );

        $discoveredOrigins = $discovered
            ->pluck('url_origin')
            ->filter()
            ->map(
                fn (string $origin): string => $this->normalizeOrigin($origin)
            );

        $this->newFoundSystems = $discovered
            ->filter(
                fn (array $record): bool => ! $configuredOrigins->contains(
                    $this->normalizeOrigin(
                        $record['url_origin']
                    )
                )
            )
            ->values()
            ->all();

        $this->orphanSystems = $configured
            ->filter(
                fn (array $record): bool => ! $discoveredOrigins->contains(
                    $this->normalizeOrigin(
                        $record['url_origin'] ?? ''
                    )
                )
            )
            ->values()
            ->all();

        $this->resetTable();
    }

    public function alerts(Schema $schema): Schema
    {
        return $schema
            ->components([

                Callout::make('newFound')
                    ->heading(
                        count($this->newFoundSystems)
                            .' New Systems Found'
                    )
                    ->description(
                        count($this->newFoundSystems)
                            ? collect($this->newFoundSystems)
                                ->pluck('url_origin')
                                ->take(5)
                                ->implode(', ')
                            : 'No new systems detected.'
                    )
                    ->icon('heroicon-o-plus-circle')
                    ->info()
                    ->actions([
                        Action::make('synchronize')
                            ->label('Synchronize Systems')
                            ->icon('heroicon-o-arrow-path')
                            ->color('info')
                            ->button()
                            ->visible(
                                fn (): bool => count($this->newFoundSystems) > 0
                            )
                            ->action(
                                fn () => $this->synchronizeSystems()
                            ),
                    ])
                    ->footerActionsAlignment(Alignment::End),

                Callout::make('orphan')
                    ->heading(
                        count($this->orphanSystems)
                            .' Orphan Systems Found'
                    )
                    ->description(
                        count($this->orphanSystems)
                            ? collect($this->orphanSystems)
                                ->pluck('url_origin')
                                ->take(5)
                                ->implode(', ')
                            : 'No orphan systems detected.'
                    )
                    ->icon('heroicon-o-exclamation-triangle')
                    ->color(
                        count($this->orphanSystems)
                            ? 'warning'
                            : 'gray'
                    ),

            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->records(
                fn (): Collection => collect(
                    app(SystemRepository::class)
                        ->active()
                )
            )
            ->recordUrl(
                fn (array $record): ?string => $record['url_origin'] ?? null
            )
            ->openRecordUrlInNewTab()
            ->columns([

                TextColumn::make('name')
                    ->searchable(),

                TextColumn::make('description')
                    ->wrap(),

                TextColumn::make('url_origin')
                    ->label('System'),

            ]);
    }

    private function normalizeOrigin(
        string $origin
    ): string {
        return rtrim(
            strtolower(trim($origin)),
            '/'
        );
    }
}
