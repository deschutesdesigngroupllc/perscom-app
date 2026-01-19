<x-filament-panels::page>
    @if ($this->submitted)
        <div class="text-center py-12">
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-success-100 dark:bg-success-500/20 mb-6">
                <x-filament::icon
                    icon="heroicon-o-check-circle"
                    class="h-10 w-10 text-success-600 dark:text-success-400"
                />
            </div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                Thank You!
            </h2>
            <p class="text-gray-600 dark:text-gray-400 max-w-md mx-auto">
                {{ $this->submissionForm->success_message ?? 'Your form has been successfully submitted.' }}
            </p>
        </div>
    @else
        @if ($this->submissionForm->instructions)
            <div class="prose dark:prose-invert max-w-none mb-6">
                {!! $this->submissionForm->instructions !!}
            </div>
        @endif

        <form wire:submit="submit">
            {{ $this->form }}

            <div class="mt-6">
                <x-filament::button type="submit" wire:loading.attr="disabled">
                    <span wire:loading.remove>Submit</span>
                    <span wire:loading>Submitting...</span>
                </x-filament::button>
            </div>
        </form>
    @endif

    <x-filament-actions::modals />
</x-filament-panels::page>