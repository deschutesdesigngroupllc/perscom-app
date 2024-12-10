@props(['unit'])

<div class="flex flex-row space-x-2 items-center justify-center bg-gray-50 dark:bg-gray-950 rounded-md first:rounded-t-none py-2 px-4 ">
  @if ($unit->icon)
    <x-filament::icon icon="{{ $unit->icon }}" class="h-5 w-5 text-gray-500 dark:text-gray-400" />
  @endif
  <div class="text-center text-gray-950 dark:text-white font-semibold text-sm">{{ $unit->name }}</div>
</div>
