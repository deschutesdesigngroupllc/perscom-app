---
paths:
  - 'app/Filament/**'
---

# Filament

## Heroicon enum for icons; literal navigation groups
Use the Filament\Support\Icons\Heroicon enum for all icons ($navigationIcon, ->icon(), emptyStateIcon), not 'heroicon-o-*' strings. Set $navigationGroup as a plain string literal (e.g. 'Organization', 'Records') and always set $navigationSort.

## Title Case labels
Write field, column, entry, filter, and action labels in Title Case: minor words (of, in, or, to,
by…) stay lowercase mid-label, and acronyms like API, ID, and RSVP stay uppercase. Unlabeled
components already default to Title Case via App\Support\Label, so only add ->label() when the
name-derived label is wrong. This rule also governs app/Forms and app/Livewire.

## No extra-small badges
Use size sm or larger for Filament badges; never xs.

## Plain modal headers
Give modals a Title Case ->modalHeading() and a ->modalDescription(); do not add ->modalIcon()
except on destructive confirmations.

## Copyable values as disabled inputs
Show a single value the user copies (URL, key, ID) on a form or page as a disabled TextInput with
->copyable() and ->dehydrated(false), not a TextEntry.
