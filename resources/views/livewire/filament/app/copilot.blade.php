<div>
  @if ($this->isAvailable())
    {{ $this->askAction }}

    <x-filament-actions::modals />
  @endif
</div>
