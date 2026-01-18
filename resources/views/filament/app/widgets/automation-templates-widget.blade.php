<x-filament-widgets::widget>
    @if (count($this->getTemplates()) > 0)
        <div class="mb-0">
            <h3 class="text-base font-bold text-primary mb-3">Quick Start Templates</h3>
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($this->getTemplates() as $key => $template)
                    <a href="{{ $this->getCreateUrl($key) }}"
                        class="group relative flex flex-col rounded-xl border border-border bg-white p-4 shadow-sm transition duration-150 hover:border-primary-500 hover:shadow-md dark:bg-gray-900 dark:hover:border-primary-500">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex-1 min-w-0">
                                <h4 class="font-medium text-primary group-hover:text-primary-600 dark:group-hover:text-primary-400">
                                    {{ $template['name'] }}
                                </h4>
                                <p class="mt-1 text-sm fi-sc-text ">
                                    {{ $template['description'] }}
                                </p>
                            </div>
                            <div class="flex-shrink-0">
                                <x-filament::icon
                                    icon="heroicon-o-arrow-right"
                                    class="h-5 w-5 text-gray-400 transition group-hover:text-primary-500 group-hover:translate-x-1"
                                />
                            </div>
                        </div>

                        @if (!empty($template['prerequisites']))
                            <div class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-800">
                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Prerequisites:</p>
                                <ul class="text-xs text-gray-400 dark:text-gray-500 space-y-0.5">
                                    @foreach (array_slice($template['prerequisites'], 0, 2) as $prereq)
                                        <li class="truncate">{{ $prereq }}</li>
                                    @endforeach
                                    @if (count($template['prerequisites']) > 2)
                                        <li class="text-gray-400">+{{ count($template['prerequisites']) - 2 }} more...</li>
                                    @endif
                                </ul>
                            </div>
                        @endif

                        @if (!empty($template['category']))
                            <div class="mt-3">
                                <x-filament::badge size="sm" color="gray">
                                    {{ $template['category'] }}
                                </x-filament::badge>
                            </div>
                        @endif
                    </a>
                @endforeach
            </div>
        </div>
    @endif
</x-filament-widgets::widget>
