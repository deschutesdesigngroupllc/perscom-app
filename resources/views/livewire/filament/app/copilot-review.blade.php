@php
  use App\Models\Enums\CopilotOperationType;
  use Illuminate\Support\Str;
@endphp

<div class="space-y-6">
  @if (filled($answer))
    <div class="prose prose-sm max-w-none dark:prose-invert">
      {!! Str::markdown($answer, ['html_input' => 'escape', 'allow_unsafe_links' => false]) !!}
    </div>
  @endif

  @if (filled($operations))
    <div class="space-y-2">
      <h3 class="text-sm font-semibold text-gray-950 dark:text-white">Proposed Changes</h3>
      <ul class="divide-y divide-gray-200 rounded-lg border border-gray-200 dark:divide-white/10 dark:border-white/10">
        @foreach ($operations as $operation)
          <li class="flex items-start gap-3 px-4 py-3 text-sm">
            <x-filament::badge :color="$operation->type->getColor()">
              {{ $operation->type->getLabel() }}
            </x-filament::badge>
            <div class="min-w-0 flex-1">
              <p class="text-gray-950 dark:text-white">{{ $operation->summary }}</p>
              <p class="text-gray-500 dark:text-gray-400">
                {{ count($operation->userIds) }} {{ Str::plural('member', count($operation->userIds)) }}
                @if (filled($operation->text) && $operation->type !== CopilotOperationType::MESSAGE)
                  &middot; {{ Str::limit($operation->text, 120) }}
                @endif
              </p>
            </div>
          </li>
        @endforeach
      </ul>
      <p class="text-xs text-gray-500 dark:text-gray-400">Nothing has been changed yet. Review the list above, then apply the changes.</p>
    </div>
  @endif
</div>
