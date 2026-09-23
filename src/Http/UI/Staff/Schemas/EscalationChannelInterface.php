<?php

declare(strict_types=1);

namespace Rimba\Menu\Http\UI\Staff\Schemas;

interface EscalationChannelInterface
{
    public function make(string $name, array $config): mixed;
}
