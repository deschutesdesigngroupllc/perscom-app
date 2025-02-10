@props(['structure' => 'unit', 'message' => null])

<div class="flex flex-row items-center justify-center py-2">
  <div class="text-gray-700 dark:text-gray-400 font-normal">
    @if (filled($message))
      {!! $message !!}
    @else
      No personnel assigned to this {{ $structure }}.
    @endif
  </div>
</div>
