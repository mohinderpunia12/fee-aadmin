<x-filament-panels::page>
    <form wire:submit.prevent="submit" class="space-y-6">
        {{ $this->form }}

        <div class="flex items-center justify-end">
            <x-filament::button type="submit">
                Add Students
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
