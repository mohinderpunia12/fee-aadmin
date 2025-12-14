<x-filament-panels::page>
    <div class="max-w-2xl mx-auto">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $this->getHeading() }}</h2>
            @if($this->getSubheading())
                <p class="mt-2 text-gray-600 dark:text-gray-400">{{ $this->getSubheading() }}</p>
            @endif
        </div>

        <x-filament-panels::form wire:submit="register">
            {{ $this->form }}

            <x-filament-panels::form.actions
                :actions="[
                    \Filament\Forms\Components\Actions\Action::make('register')
                        ->label('Create Account & Start Trial')
                        ->submit('register')
                        ->color('primary')
                        ->size('lg'),
                ]"
                :full-width="true"
            />
        </x-filament-panels::form>
    </div>
</x-filament-panels::page>
