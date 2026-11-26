# Livewire Components

Standalone Livewire components and the traits they share. `App/` holds the panel banners mounted
through render hooks, `Widgets/` backs the embeddable widget views in `resources/views/widgets`,
`Filament/App/` holds components embedded in app panel pages, and `Concerns/` holds traits that
host components mix in. This file covers the event pieces: RSVPs and the event discussion.

## Event RSVPs

`Concerns/RespondsToEvents` gives a Livewire component the `respondToEvent()` method that the
`<x-events.rsvp-buttons>` Blade component calls with `wire:click`. That component lives in
`resources/views/components/events/rsvp-buttons.blade.php`. The buttons target whichever Livewire
component renders them, so every component that renders the buttons must use the trait. The hosts
are:

| Host                                                           | Listener for `event-response-updated` |
|----------------------------------------------------------------|---------------------------------------|
| `app/Filament/App/Resources/EventResource/Pages/ViewEvent.php` | `refreshOccurrence()`                 |
| `app/Filament/App/Pages/Calendar.php`                          | `refreshCalendar()`                   |
| `app/Filament/App/Widgets/UpcomingEventsWidget.php`            | `refreshEvents()`                     |

The flow through `respondToEvent($eventId, $starts, $status, $wholeSeries)`:

1. Load the event and abort with 403 unless there is a signed-in `User` who can `view` it.
2. Resolve the occurrence with `EventOccurrenceService::find()` from the `$starts` unix timestamp,
   and the response with `EventRegistrationStatus::tryFrom()`. Either missing aborts with 404.
3. Hand off to `EventRegistrationService::respond()`, which owns every business rule: only
   `EventRegistrationStatus::memberResponses()` are accepted, and cancelled, ended, closed, or
   past-deadline occurrences are refused. Refusals arrive as `EventRegistrationException` and are
   shown as a danger notification, not thrown.
4. Dispatch `event-response-updated` with `eventId` and `starts`, whether the response saved or
   not, so hosts drop their cached occurrences and registrations.

The method is public, so a client can call it with any arguments. Keep the 403 and 404 guards in
the trait and the RSVP rules in the service; the Blade component's `canRespond()` check only hides
the buttons.

`$wholeSeries` only matters for repeating events: the service stores a series-wide registration
(null `event_occurrence_id`) and clears the member's future per-date responses. One-time events
always store series-wide. Only `ViewEvent` passes `wholeSeries`; the other hosts answer per date.

A new host needs the trait, an `#[On('event-response-updated')]` method that clears whatever it
memoized, and a test that calls `respondToEvent` and asserts the dispatch (see
`tests/Feature/Tenant/Filament/App/Pages/CalendarTest.php`).

## Event Discussion

`Filament/App/EventDiscussion` is the comment thread on the event view page. `ViewEvent::content()`
embeds it with `Livewire::make(EventDiscussion::class, ...)` keyed `event-discussion`, and its view
is `resources/views/livewire/filament/app/event-discussion.blade.php`.

Comments are the generic `App\Models\Comment` attached through the `HasComments` trait's
`model_id`/`model_type` morph. `HasAuthor` sets `author_id` from the signed-in user on create, so
`post()` passes only the comment body. Nothing notifies anyone when a comment is posted.

Permissions come from `app/Policies/CommentPolicy.php` (Shield permissions such as
`create_comment`):

| Ability          | Allowed when                                        |
|------------------|-----------------------------------------------------|
| See the thread   | Can open the event page (no check in the component) |
| Post             | `can('create', Comment::class)`                     |
| Edit a comment   | The viewer wrote it, or holds `update` on it        |
| Delete a comment | The viewer wrote it, or holds `delete` on it        |

Authors manage their own comments without any comment permission; the permissions are for
moderators. `ViewEvent` skips the resource `viewAny` gate so members can open events, so the
component is reachable by any member who can view the event.

Things to keep when changing it:

- `findComment()` scopes the lookup to this event with `whereMorphedTo()`. The edit and delete
  actions take the comment id from client arguments, so dropping that scope would let a member
  edit or delete a comment on another record.
- The edit and delete actions re-check `canManage()` inside `->action()` as well as in
  `->visible()`, because a hidden action can still be called.
- Comment HTML is stored as the rich editor sends it and sanitized at render with
  `Str::sanitizeHtml()` in the Blade view. Any other place that prints a comment must sanitize it
  too.
- `comments()` loads authors with a separate keyed query instead of `with('author')`. A comment
  whose author no longer exists renders as "Former member".
- Comments list oldest first. "(edited)" shows when `updated_at` is more than a minute after
  `created_at`.

Tests live in `tests/Feature/Tenant/Livewire/Filament/App/EventDiscussionTest.php`, including one
that renders the thread inside `ViewEvent`.
