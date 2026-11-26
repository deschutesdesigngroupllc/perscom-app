@props(['structure' => 'unit', 'message' => null])

<div class="px-4 pb-3 text-sm text-gray-500 dark:text-gray-400">
  @if (filled(Str::of($message)->stripTags()))
    {!! $message !!}
  @else
    No members assigned to this {{ $structure }}.
  @endif
</div>
