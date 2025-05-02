@props(['message' => null])

<div class="flex flex-row items-center justify-center py-4">
  <div class="text-gray-700 dark:text-gray-400 font-normal">
    @if (filled($message))
      {!! $message !!}
    @else
      No units assigned to this group.
    @endif
  </div>
</div>
