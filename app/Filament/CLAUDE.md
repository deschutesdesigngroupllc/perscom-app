# Filament Panels

This directory holds the Filament UI. `App/` is the tenant dashboard (the `app` panel in
`app/Providers/Filament/AppPanelProvider.php`), `Admin/` is the central `admin` panel, and `Public/`
is the `public` panel. `Actions/`, `Concerns/`, `Forms/`, `Tables/`, and `Exports/` are shared
building blocks used across them.

## Navigation Containers

**Records.** `App/Pages/ServiceRecords.php` is the sidebar parent for every record type and an
overview of counts per type. It lives at `/records` because `service-records` is already the slug
of `ServiceRecordResource`. Each record resource sits in the `Records` navigation group with
`$navigationParentItem = 'Records'`, which must match the page title.

- `ServiceRecords::RECORD_RESOURCES` drives both `canAccess()` (any resource's `canViewAny()`) and
  the cards. A new record type needs its resource added to that list, the group and parent item
  set, and `tests/Feature/Tenant/Filament/App/Pages/ServiceRecordsTest.php` still passing.
- The totals and "last 30 days" counts are unscoped `Model::query()->count()` over the whole table.
  Access is checked per type, so a member who can view a type sees its full count.

**Integrations.** `App/Clusters/Integrations.php` is the cluster for everything that connects to
outside services. Its members split into two navigation groups inside the cluster:

| Group        | Members                                                                              |
|--------------|--------------------------------------------------------------------------------------|
| `Services`   | `ConnectedServices` (Discord, Slack, SSO, and SMS) and `ConnectAiTools` (MCP)        |
| `Developers` | API keys, API logs, OAuth apps, webhooks, webhook logs, and the `Widgets` embed page |

Only the two `Services` pages live under `App/Clusters/Integrations/Pages/`; the developer
resources stay in `App/Resources/` and `App/Pages/Widgets.php` and join by setting
`$cluster = Integrations::class`. Log resources nest under their parent with
`$navigationParentItem` (`API Keys`, `Webhooks`). `ConnectedServices` and `ConnectAiTools` are
super-admin only and hidden in the demo environment through their own `canAccess()`. The grouping
is asserted in `ServiceRecordsTest::test_integrations_live_in_one_cluster`, so moving a member
means updating that test.

## Calendar Links

Two actions hand events to outside calendar apps. Both delegate URL building and iCalendar output
to `app/Services/CalendarFeedService.php`.

`App/Actions/SubscribeToCalendarAction.php` is a header action on `App/Pages/Calendar.php` and a
link in `App/Widgets/UpcomingEventsWidget.php`. It shows the member's subscription links (Google,
webcal for Apple, Outlook, a copyable URL, and a QR code of the webcal URL) rendered by
`resources/views/filament/app/calendars/subscribe.blade.php`.

- The `calendar` select uses the sentinel `all` for the personal feed
  (`app.calendar-feeds.personal`); any other value is a `Calendar` id for
  `app.calendar-feeds.calendar`. Every calendar is listed, because the feed controllers filter
  each event through the `view` policy for the token's owner.
- Feed URLs carry `users.calendar_feed_token` and are served without a session, throttled by
  `calendar-feeds`. The same token signs the RSS feeds (`app.rss-feeds.*`), so **Reset Link**
  (`CalendarFeedService::resetToken()`) breaks the member's calendar and RSS subscriptions alike.
- The action is visible only to members who can `view` `Event::class`, the same check the feed
  controllers make.

`App/Resources/EventResource/Actions/AddToCalendarAction.php` is an `ActionGroup` on `ViewEvent`
for one occurrence: download `.ics` (`app.events.calendar`) or open a pre-filled Google Calendar
event. `ViewEvent` passes the selected occurrence through `->occurrence()`; without one it falls
back to `EventOccurrenceService::nearest()`. The download sends the occurrence's start as a Unix
timestamp in `occurrence`, and `Http/Controllers/App/Events/CalendarController.php` 404s when no
occurrence starts at that time. When no occurrence resolves, the download exports the whole event
with its repeat rule and the Google item is hidden.

## Submission Status

`App/Resources/SubmissionResource/Actions/ChangeSubmissionStatusAction.php` (`changeStatus`) is
the decision action on `ViewSubmission`. It attaches a new row to `statuses()` (the
`model_has_statuses` morph pivot from `app/Traits/HasStatusRecords.php`) with an optional `text`
note; it never edits or removes earlier rows. The current status is the newest pivot row by
`created_at`, so the history is append-only and two attaches in the same second have no defined
order. The action is hidden unless the user can `update` the submission and at least one `Status`
exists. Covered by `tests/Feature/Tenant/Filament/App/Resources/SubmissionReviewTest.php`.

## Task Assignees

`App/Resources/TaskResource/RelationManagers/AssigneesRelationManager.php` manages the `users`
relation of a task through the `TaskAssignment` pivot (`->as('assignment')`).

- **Assign Members** uses `syncWithoutDetaching()`. New rows fire
  `TaskAssignmentObserver::created()`, which sends `NewTaskAssignment`. Members already assigned
  get no notification, but their `assigned_at`, `assigned_by_id`, and `due_at` are overwritten,
  including clearing `due_at` when the field is left empty.
- **Mark Complete** sets `completed_at` through `updateExistingPivot()`.
- `statusFor()` derives the badge (`Completed`, `Overdue`, `Due`, or `Open`) from `completed_at`
  and `due_at` only. It ignores `expires_at` and is separate from `TaskAssignmentStatus` and the
  pivot's query scopes.
- `isReadOnly()` returns `false` so the actions show on any owner page.

Covered by `tests/Feature/Tenant/Filament/App/Resources/TaskAssigneesTest.php`.

## Adding a Member

`App/Resources/UserResource/Pages/CreateUser.php` is a two-step wizard: **Identity** (name, email,
invite toggle) and **Placement** (slot or unit, position, specialty, rank, and status). The user is
created with `approved = true`, and placement is never written onto the profile columns. Instead
it becomes `ProposedOperationData` applied through `app/Actions/Copilot/ApplyChangeSet.php` with
the source `add member`, so the member starts with assignment, rank, and status records and an
activity log entry, and the usual record observers run.

- In manual roster mode (`AssignUsersBulkAction::usesManualRoster()`) only the slot select shows;
  the slot supplies unit, position, and specialty. Otherwise unit, position, and specialty show
  separately. The assignment operation is built by `AssignUsersBulkAction::operationFor()` and is
  only added when a slot or unit is chosen, so a position or specialty without a unit is dropped.
- A rank becomes a `PROMOTION` rank record.
- Each placement field shows only when the creator may record it: the slot, unit, position, and
  specialty need `create` on `AssignmentRecord`, the rank `create` on `RankRecord`, and the status
  `update` on `User`, the same checks `ApplyChangeSet` makes.
- The member and their records are created in one transaction (`createMemberWithRecords()`), so a
  record `ApplyChangeSet` refuses rolls the member back too. The invite is sent after the
  transaction commits.
- `send_invite` is `dehydrated(false)` and read from `getRawState()`. It only applies when the
  creator can `create` `Invite::class`; a `SendInvite` validation failure becomes a warning
  notification instead of failing the page.

Covered by `tests/Feature/Tenant/Filament/App/Resources/AddMemberWizardTest.php`.

## Contained Wizards

`Concerns/HasContainedWizard.php` replaces Filament's create-page `HasWizard` so the step header,
fields, and buttons render in one card (`->contained()`). Its `getWizardComponent()` is a copy of
Filament's in `vendor/filament/filament/src/Resources/Pages/Concerns/HasWizard.php` with only
`contained(false)` flipped, so a Filament upgrade that changes that method needs the same change
here. Use the trait, not `HasWizard`, on new wizard create pages; `CreateUser` and `CreateMessage`
use it, and `AddMemberWizardTest` asserts the `fi-contained` class.

## Multi-Factor Authentication

The `app` panel registers app and email MFA with `isRequired: true` and swaps the required
middleware for `app/Http/Middleware/EnsureMultiFactorAuthenticationIsRequired.php`. Filament reads
`isRequired` when routes are registered, before a tenant is known, so the panel always says
required and the middleware decides per request: it passes through unless the tenant's
`PermissionSettings::$require_multi_factor_authentication` is on, then defers to Filament's
redirect to MFA setup. Super admins toggle the setting on the Security tab of
`App/Clusters/Settings/Pages/Permission.php`. MFA is not registered at all in the demo environment.
Covered by `tests/Feature/Tenant/Http/Middleware/EnsureMultiFactorAuthenticationIsRequiredTest.php`.
