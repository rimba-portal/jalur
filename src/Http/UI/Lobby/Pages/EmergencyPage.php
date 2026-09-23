<?php

declare(strict_types=1);

namespace Rimba\Menu\Http\UI\Lobby\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Rimba\Menu\Http\UI\Staff\Schemas\EscalationDataLoader;
use Rimba\Menu\Http\UI\Staff\Schemas\EscalationSchemaBuilder;

class EmergencyPage extends Page implements HasSchemas
{
    use InteractsWithSchemas;

    protected static string|BackedEnum|null $navigationIcon = 'bites-l-emergency';

    protected static ?string $navigationLabel = 'Emergency';

    protected static ?int $navigationSort = 4;

    protected static ?string $title = 'Emergency';

    protected ?string $subheading = 'Emergency Response Escalation Matrix';

    protected string $view = 'bites::pages.call';

    public function contactInfolist(
        Schema $schema
    ): Schema {

        $escalationDataLoader = app(EscalationDataLoader::class);
        $escalationSchemaBuilder = app(EscalationSchemaBuilder::class);

        return $schema->schema(
            $escalationSchemaBuilder->build(
                $escalationDataLoader->get('emergency'),
                2,
                true
            )
        );
    }
}
