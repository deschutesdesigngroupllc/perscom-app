# App Dashboard Widgets

This directory holds the Livewire widgets on the tenant app panel's dashboard
(`app/Filament/App/Pages/Dashboard.php`): the owner's launch checklist, the admin to-do list, the
signed-in member's service record and upcoming events, and the shared base class behind the
announcement and news cards. Each widget renders a Blade view under
`resources/views/filament/app/widgets/`, and its tests live in
`tests/Feature/Tenant/Filament/App/Widgets/`.

## Registration and Order

`app/Providers/Filament/AppPanelProvider.php` discovers this directory and also lists the
dashboard widgets in `->widgets([...])`. The dashboard renders every panel widget
(`Filament::getWidgets()`), so any concrete widget class added here appears on it, subject to its
`canView()`. Widgets under a resource's `Widgets` folder (`RecentAnnouncements`, `RecentNews`,
`UsersOverview`) are not discovered and show only because they are in the explicit list.
Discovery skips abstract classes, so `RecentPostsWidget` never renders on its own.

Order comes from `$sort`, lowest first:

| Widget                                 | `$sort` | Who sees it                       |
|----------------------------------------|---------|-----------------------------------|
| `LaunchPlanWidget`                     | -30     | Super admin, until done or hidden |
| `NeedsAttentionWidget`                 | -20     | Anyone who can act on one item    |
| `ConnectAiAgentWidget`                 | -10     | Own `canView()`, dismissible      |
| `OrganizationInfoWidget`               | -5      | Shield permission and super admin |
| `UserProfileWidget`                    | -5      | Shield permission                 |
| `UpcomingEventsWidget`                 | -4      | Shield permission                 |
| `RecentRecordsWidget`, `UsersOverview` | -3      | Shield permission                 |
| `RecentAnnouncements`, `RecentNews`    | -2      | Shield permission                 |
| `CalendarWidget`                       | -1      | Shield permission                 |

Most widgets set `$isLazy = false` and `$columnSpan = 'full'`, so they render in the first page
response and stack as full-width cards.

## Permissions

Two visibility models live side by side:

- **Shield-gated widgets** use `HasWidgetShield`, whose `canView()` checks the generated
  `view_<snake_class>` permission. Those permissions are seeded for the `Admin` and `User` roles in
  `database/seeders/ShieldSeeder.php`; a new Shield-gated widget needs its permission added there
  for both roles, or members never see it. A permission only controls whether the card shows; what
  is inside still goes through policies (see below).
- **Self-gated widgets** (`LaunchPlanWidget`, `NeedsAttentionWidget`, `ConnectAiAgentWidget`)
  override `canView()` and do not use the trait, so a Shield permission generated for them has no
  effect.

`NeedsAttentionWidget::canView()` must stay the union of the per-item checks in `getItems()`. Each
item is built only when the viewer passes its own check: `approve` on `User`, `viewAny` on
`Submission` and `Invite`, and `PromotionBoard::canAccess()` and `Readiness::canAccess()` for the
promotion and expiring items. If a new item's check is left out of `canView()`, a viewer who can
only act on that item never sees the card; if `canView()` grants more than the items, the viewer
sees an empty "All clear" card.

Inside the member widgets, content is filtered by policy, not by the widget permission:

- `UpcomingEventsWidget` returns no occurrences unless the member can `view` `Event`, then drops
  each occurrence whose event the member cannot view.
- `UserProfileWidget` links to a profile record tab only when the relation manager's
  `canViewForRecord()` passes for the member.
- The `RecentPostsWidget` subclasses offer the RSS link only when the member can `view` the model,
  because `RssFeedService` would refuse the feed otherwise, and the "View All" link only when the
  resource's `canViewAny()` passes.

## Launch Plan

`LaunchPlanWidget::getSteps()` derives every step from data each time it runs; nothing is ticked by
hand. Notable rules:

- `profile` counts as done only when the owner's name is no longer `Admin`, the placeholder name
  `App\Actions\Tenant\SetupTenantAccount` gives the founder when signup has no founder name.
- `members` is done when users plus pending invites exceed `MEMBER_TARGET`, so the founder does not
  count toward the target.
- `url` links to the Dashboard settings page with `?tab=domain::data::tab`; renaming that tab
  breaks the link, and a test asserts it.
- `structure` uses the same rank, unit, and position check as `Dashboard::hasStructure()`, which
  decides whether the setup wizard opens on its own. Keep the two in step.

`canView()` builds a fresh instance and runs `getSteps()` to decide whether any step is open, and
the view runs it again, so each step's check must stay a cheap `exists()` or `count()` query.

Hiding is per user. `dismiss()` adds `DISMISSED_KEY` to the `dismissed_widgets` user setting
through `UserSettingsService`, which stores the value keyed by user id in the tenant's
`App\Settings\UserSettings`, so one super admin hiding the plan does not hide it for another.
`ConnectAiAgentWidget` uses the same setting with its own key; a new dismissible widget picks a
unique key and follows the same pattern.

## Needs Attention

Each item in `NeedsAttentionWidget::getItems()` is a count plus a link to the page where it gets
handled, and items with a zero count are dropped. The counts come from:

- `User` with `approved = false`, which is also what `UserResource::getNavigationBadge()` shows.
- `Submission::unread()` and `Invite::pending()` scopes.
- `PromotionEligibilityService::eligibleCount()`, cached for ten minutes per tenant, so the badge
  can lag a promotion by up to that long.
- `ReadinessService::expiringItems()`, using its `EXPIRING_WINDOW_DAYS` (30) window. The label
  hard-codes "30 Days", so changing the constant means changing the label.

To add an item, add an entry with its own permission check to `getItems()`, add the same check to
`canView()`, and give it a `key` the tests can assert on. The view widens its grid for up to five
items.

## Member Widgets

`UserProfileWidget` and `UpcomingEventsWidget` always describe the signed-in user from
`Auth::user()`; they take no record. Both memoize in `protected` properties
(`$promotion`, `$relationManagers`, `$occurrences`, `$registrations`). Livewire does not persist
protected properties, so the memo lasts one request and is rebuilt on the next.

`UserProfileWidget` links into profile tabs by index (`?relation=<n>`). The tabs come from
`ViewUser::profileRelationManagers()`, which drops record types the organization hides through the
`user_hidden_fields` Dashboard setting, so the index of a tab moves when another type is hidden.
Always resolve a tab through `relationIndex()`; never hard-code a number. The awards rack shows
only when the award records tab is shown. Promotion progress comes from
`PromotionEligibilityService::evaluate()` and appears only when there is a next rank with
requirements.

`UpcomingEventsWidget` expands occurrences with `EventOccurrenceService::between()` over the next
`DAYS_AHEAD` days, then drops cancelled and ended ones. Behavior to keep in mind:

- The featured occurrence is one happening now, otherwise the soonest; the agenda is the rest,
  grouped by day in the member's timezone from `UserSettingsService::timezone()`.
- `statusFor()` loads the member's registrations for every shown event in one query. A response to
  a single occurrence of a repeating event wins over the member's series-wide response.
- RSVP buttons call `respondToEvent()` from `App\Livewire\Concerns\RespondsToEvents`, which
  dispatches `event-response-updated`. `refreshEvents()` listens for it and clears the memo, as do
  `Calendar` and `ViewEvent`.
- `externalUrl()` reads the raw `url` column because the `Event` `url` accessor falls back to the
  event's own app URL, which is not an external link.
- Check-in is offered only inside `EventAttendanceService::isWithinCheckInWindow()`, and only to a
  member who responded or for an event with registration enabled, and never after attending.

## Recent Posts

`RecentPostsWidget` is an abstract card shared by
`app/Filament/App/Resources/AnnouncementResource/Widgets/RecentAnnouncements.php` and
`app/Filament/App/Resources/NewsfeedResource/Widgets/RecentNews.php`. A subclass supplies copy,
links, and record accessors; the base class turns `records()` into
`App\Data\Widgets\RecentPostData`, features the first post, and opens any post in the `view`
action's modal.

When adding a feed:

- Put the subclass under its resource's `Widgets` folder, use `HasWidgetShield`, set `$sort`, add
  it to the panel's widget list, and seed its `view_` permission in `ShieldSeeder`.
- `records()` returns at most `LIMIT` posts, newest first, eager loading whatever `authorFor()`
  reads.
- `findRecord()` resolves the key the modal receives from the browser. It must apply the same
  scoping as `records()`, because a member can send any key; returning `null` hides the action,
  which is also how a post deleted after the page loaded is handled.
- Override `authorFor()`, `dateFor()` (defaults to `created_at`), and `noteFor()` as needed.

The excerpt strips tags and is limited to 280 characters. The modal renders `bodyFor()` through
`TextEntry::html()`, which sanitizes it.
