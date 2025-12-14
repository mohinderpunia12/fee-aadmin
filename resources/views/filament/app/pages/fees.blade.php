<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Fee Payments Table --}}
        <div>
            {{ $this->table }}
        </div>

        {{-- Add Fee Payment Form --}}
        @if($this->showForm)
            <div class="fi-section rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold">Add Fee Payment</h3>
                    <x-filament::button 
                        color="gray" 
                        wire:click="cancelForm"
                        size="sm">
                        Cancel
                    </x-filament::button>
                </div>
    <form wire:submit.prevent="submit" class="space-y-6">
        {{ $this->form }}

                    <div class="flex items-center justify-end gap-3">
                        <x-filament::button 
                            color="gray" 
                            wire:click="cancelForm"
                            type="button">
                            Cancel
                        </x-filament::button>
            <x-filament::button type="submit">
                Save Fee Payment
            </x-filament::button>
        </div>
    </form>
            </div>
        @endif
    </div>
</x-filament-panels::page>

