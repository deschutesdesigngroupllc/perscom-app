@props(['slot', 'vacant' => false])

<div class="flex items-center gap-2 border-t border-gray-950/5 px-4 pt-3 pb-1.5 dark:border-white/5">
  <span class="text-xs font-semibold tracking-wide text-gray-500 uppercase dark:text-gray-400">{{ $slot->name }}</span>
  @if ($vacant)
    <span class="text-xs text-gray-400 dark:text-gray-500">· Vacant</span>
  @endif
</div>
