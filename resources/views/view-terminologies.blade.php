<x-filament-panels::page>
    @php
        $categories = $this->getCategoriesWithCounts();
    @endphp

    <!-- Native Filament Horizontal Tabs Wrapper Layout -->
    <x-filament::tabs label="Terminology Categories">
        <!-- Default Fallback All Tab Button -->
        <x-filament::tabs.item :active="$activeTab === 'all'" wire:click="$set('activeTab', 'all')">
            All
        </x-filament::tabs.item>

        <!-- Generate a tab header dynamically for each unique category found -->
        @foreach ($categories as $categoryName => $count)
            @if (filled($categoryName))
                <x-filament::tabs.item
                    :active="$activeTab === $categoryName"
                    wire:click="$set('activeTab', '{{ $categoryName }}')"
                >
                    {{ ucfirst((string) $categoryName) }}
                    <x-slot name="badge">{{ $count }}</x-slot>
                </x-filament::tabs.item>
            @endif
        @endforeach
    </x-filament::tabs>

    <!-- Standard Filament Table Component Wrapper -->
    <div class="mt-4">{{ $this->table }}</div>
</x-filament-panels::page>
