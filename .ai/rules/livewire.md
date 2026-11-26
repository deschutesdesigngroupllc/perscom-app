---
paths:
  - 'app/Livewire/**'
---

# Livewire

## Class-based Livewire components
Write Livewire components as strict-typed classes extending Component with a matching Blade view under resources/views/livewire. Use them as nested/embedded widgets, not routed full pages.

## Title Case labels
Write field, column, entry, filter and action labels in Title Case: minor words (of, in, or, to, by…) stay lowercase mid-label, acronyms like API, ID and RSVP stay uppercase. Unlabelled components already default to Title Case via App\Support\Label, so only add ->label() when the name-derived label is wrong.
