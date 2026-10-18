@props(['structure' => 'unit', 'message' => null])

<x-filament::empty-state :contained="false" heading-tag="div" icon="heroicon-o-user-group" icon-color="gray" :heading="filled(Str::of($message)->stripTags())
    ? new \Illuminate\Support\HtmlString($message)
    : 'No personnel assigned to this ' . $structure . '.'"
  class="px-4 py-6" />
