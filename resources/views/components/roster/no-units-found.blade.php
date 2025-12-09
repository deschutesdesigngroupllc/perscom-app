@props(['message' => null])

<div class="flex flex-row items-center justify-center py-4">
  <div class="text-sm text-gray-600 dark:text-gray-400 font-normal">
    @if (filled(Str::of($message)->stripTags()))
      {!! $message !!}
    @else
      No units assigned to this group.
    @endif
  </div>
</div>
