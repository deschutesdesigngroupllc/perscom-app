@props(['insight' => null])

@if ($insight)
  @if ($insight->promotionEligible)
    <x-filament::badge color="success" icon="heroicon-m-chevron-double-up" size="sm" :tooltip="$insight->nextRank ? 'Eligible for promotion to ' . $insight->nextRank : 'Eligible for promotion'">
      Promotion Eligible
    </x-filament::badge>
  @endif
  @if ($insight->hasExpired)
    <x-filament::badge color="danger" icon="heroicon-m-exclamation-triangle" size="sm"
      tooltip="Has an expired credential or qualification">
      Expired
    </x-filament::badge>
  @elseif ($insight->hasExpiring)
    <x-filament::badge color="warning" icon="heroicon-m-clock" size="sm"
      tooltip="Has a credential or qualification expiring within 30 days">
      Expiring Soon
    </x-filament::badge>
  @endif
@endif
