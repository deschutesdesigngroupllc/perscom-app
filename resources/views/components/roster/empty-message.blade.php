@props(['structure' => 'unit', 'message' => null])

<div class="px-4 py-3 text-center text-gray-500 dark:text-gray-400">
  @if (filled(Str::of($message)->stripTags()))
    {!! $message !!}
  @else
    No personnel assigned to this {{ $structure }}.
  @endif
</div>
