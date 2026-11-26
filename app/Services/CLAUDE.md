# Services

Static service classes that hold the application's domain logic, called from controllers, Filament
pages, jobs, observers, and MCP tools. This file covers the services that face outside the
dashboard: token-based calendar and RSS feeds, service profile share images, the internal API
bridge, and the Discord and Slack integrations, along with the controllers, middleware, and
notification channel that sit around them.

## Private Feeds

`CalendarFeedService` and `RssFeedService` serve feeds to calendar apps and feed readers, which
cannot hold a session. Both are authenticated by one secret, `users.calendar_feed_token`:

- `CalendarFeedService::tokenFor()` creates the 40-character token on first use, and
  `resetToken()` replaces it. Resetting revokes every calendar and RSS link at once, which is why
  the Reset Link action in `app/Filament/App/Actions/SubscribeToCalendarAction.php` warns about
  both. `RssFeedService` has no token of its own; reuse `tokenFor()` for any new feed.
- The routes live in `routes/app.php` under `feeds/{token}` outside `auth:web`, each group with its
  own IP rate limiter (`calendar-feeds`, `rss-feeds`) defined in
  `app/Providers/RouteServiceProvider.php`.
- Every feed controller resolves the member with `CalendarFeedService::findUser()`, answers 404 for
  an unknown token, and 403 when the member fails the model-level `view` policy (`Event`,
  `Announcement`, or `Newsfeed`). A new feed controller must do the same two checks.
- Responses set `Cache-Control: private, max-age=900` and `X-Robots-Tag: noindex`; use the
  services' `toResponse()` rather than building headers by hand.

| Route                         | Controller                    | Output                          |
|-------------------------------|-------------------------------|---------------------------------|
| `app.calendar-feeds.personal` | `PersonalFeedController`      | All viewable events, `.ics`     |
| `app.calendar-feeds.calendar` | `CalendarFeedController`      | One calendar's events, `.ics`   |
| `app.events.calendar`         | `Events\CalendarController`   | One event or occurrence, `.ics` |
| `app.rss-feeds.announcements` | `AnnouncementsFeedController` | Latest 25 announcements, RSS    |
| `app.rss-feeds.news`          | `NewsFeedController`          | Latest 25 newsfeed items, RSS   |

### Calendar Feeds

`feedEvents()` loads one-time events that ended within `FEED_HISTORY_DAYS` (90) days or later, and
repeating events whose schedule's `last_occurrence` is null or within that window, then filters
each through `$user->can('view', $event)` in PHP. The per-calendar feed relies on that filter, not
on any check of the calendar itself.

`components()` writes the iCalendar output that `App\Support\ICalendar` renders:

- A one-time event is a single `VEVENT` in UTC.
- A repeating event is one series `VEVENT` with an `RRULE` from
  `ScheduleService::generateRecurringRule()`, written in local time with a `TZID` from
  `ScheduleService::timezone()` so recurrences keep their wall-clock time across DST. Cancelled
  occurrences become `EXDATE` values, and occurrences with notes or a changed end time become
  override `VEVENT`s with a `RECURRENCE-ID`.
- All-day events use `DATE` values, an exclusive `DTEND`, and a date-only `UNTIL` in the rule.
- `UID`s include the tenant key and the `app.url` host so they stay unique across organizations.
  `renderOccurrence()` gives a single occurrence of a repeating event its own timestamped `UID` so
  it does not collide with the series.
- `URL` is the event's raw external `url` when set, otherwise the event's page in the app panel.
  `DESCRIPTION` is the HTML description and occurrence notes flattened to plain text, followed by
  that page link.

Change the iCalendar output with `tests/Feature/Tenant/Services/CalendarFeedServiceTest.php` and the
controller tests under `tests/Feature/Tenant/Http/Controllers/App/Calendars/`.

### RSS Feeds

`RssFeedService::render()` writes RSS 2.0 with `XMLWriter`. Items link to the dashboard, not to the
record, and use a non-permalink `guid` of `announcement-{id}` or `news-{id}`. The announcements feed
does not filter on `enabled` or `expires_at`, matching the Recent Announcements widget. The news
feed reads `Newsfeed`, whose global `NewsfeedScope` limits it to the `newsfeed` activity log. The
`RecentNews` and `RecentAnnouncements` widgets show the feed links.

## Service Profile Share Image

`ServiceProfileImageService::render()` draws the 1200x630 Open Graph image for a public service
profile with GD, using the Inter fonts in `resources/fonts/`. It takes a `ServiceProfileData` from
`ServiceProfileService::build()` and honors its `showsRank` and `showsAssignment` flags, so a
hidden rank or assignment stays out of the image.

- It is served by `app/Http/Controllers/App/ServiceProfiles/ImageController.php`, which returns 404
  unless `ServiceProfileService::isPublic()` holds.
- The PNG is cached for a day under a key built from the member's `updated_at`, and the `og:image`
  URL carries the same timestamp as `v`. A change that does not touch the member's `updated_at`
  (a new award record, a renamed rank) is not reflected until the cache entry expires.
- Only raster images are drawn. SVG insignia and ribbons, and any file GD cannot decode, are
  skipped; a rank without a usable image falls back to an abbreviation badge. Ribbons stop at the
  right padding rather than wrapping.

## Internal API Bridge

`InternalApiService` lets the MCP tools in `app/Mcp/` call the REST API in-process instead of
duplicating its queries. `send()` builds a sub-request to `config('api.url')` and
`config('api.version')`, hands it to the HTTP kernel, and in a `finally` block restores the original
request and clears the facade's resolved instance.

- The caller's `Authorization` header is forwarded, so the API's authentication, scopes, policies,
  validation, and resources apply exactly as for an external client. There is no way to call it as
  another user or to bypass a policy.
- The current tenant is pinned with the `InitializeTenancyByRequestData::$header` (`X-Perscom-Id`)
  header, so the sub-request cannot land in another organization.
- The JSON body is set as the request content the way `Request::capture()` does, because Orion
  reads search parameters from the request bag.
- `json_encode()` failures throw `JsonException`; HTTP errors come back as the response, not as
  exceptions.

Covered by `tests/Feature/Tenant/Services/InternalApiServiceTest.php`.

## Discord and Slack Installs

Discord and Slack redirect every organization to one OAuth callback on the central auth domain,
`integrations.install.callback` in `routes/integrations.php`. The flow is:

1. `App\Integrations\InstallController` (super admin only) calls
   `IntegrationInstallService::state()`, which stores a random nonce in the session and returns an
   encrypted state holding the provider, tenant key, nonce, and the host the admin started on.
2. `Integrations\InstallCallbackController` decrypts the state and redirects to the tenant's
   `app.integrations.{provider}.connect` route with the query string intact. `returnUrlFor()` only
   returns the starting host when it is one of the tenant's `custom_url`, `fallback_url`, or `url`
   hosts, so the state cannot redirect to a foreign site and the code reaches the session that holds
   the nonce.
3. `DiscordConnectController` or `SlackConnectController` call
   `CompletesIntegrationInstall::completeInstall()`, which requires the super admin role, pulls the
   nonce with `IntegrationInstallService::verify()` (single use, provider and tenant must match),
   and only then runs the closure that exchanges the code and calls `ConnectDiscordServer` or
   `ConnectSlackWorkspace`.

Every outcome, including a denied authorization, an expired or foreign state, and an unreachable
provider, ends in a Filament notification and a redirect to the provider's tab on
`ConnectedServices`; a non-owner is sent to the dashboard. Throw `IntegrationConnectionException`
from the closure for a message the admin should see. A new OAuth provider needs an `installUrl()`
on its service, an entry in the `whereIn('provider', ...)` lists in both route files, a connect
route named `app.integrations.{provider}.connect`, and a connect controller using the trait. The
tests are `DiscordConnectControllerTest` and `SlackConnectControllerTest` under
`tests/Feature/Tenant/Http/Controllers/App/Integrations/`.

## Discord Slash Commands

`POST integrations/discord/interactions` on the central auth domain runs
`app/Http/Middleware/VerifyDiscordSignature.php` and then `DiscordInteractionService::handle()`.
The middleware verifies the Ed25519 signature over the timestamp and raw body against
`services.discord.public_key` and answers 401 otherwise; Discord rejects an endpoint that accepts
unsigned requests.

`handle()` answers `PING` before anything else, then resolves the organization from the
interaction's `guild_id`: with tenancy enabled, through the central `tenants.discord_guild_id`
column that `ConnectDiscordServer` sets (one server per organization); without it, by comparing
against `discord_settings.discord_server`. Commands from DMs or unconnected servers get a polite
reply. It initializes tenancy for the request and always ends it and calls `Auth::forgetGuards()`
in a `finally` block.

- The invoker is the approved member whose `discord_user_id` matches, set on the `web` guard for
  the interaction so policies and `Roster::canAccess()` apply as in the dashboard. An unlinked
  invoker is treated as a guest.
- `/profile` shows a public profile to anyone and a private one only to the member themselves or
  someone who can `view` them. `/roster` requires roster page access; `/events` and `/rsvp`
  require `view` on `Event` and on the event. RSVPs go through
  `EventRegistrationService::respond()`, so the same status and window rules apply.
- Replies are ephemeral unless meant to be shared (a public `/profile`), and set
  `allowed_mentions` to parse nothing so a reply never pings anyone.
- Autocomplete resolves the invoker and applies the same checks as the commands: unit suggestions
  need roster access, event suggestions need `view` on `Event` and on each event, and a Discord
  user with no linked member gets none. The `/rsvp` choice value is `OccurrenceData::key()`
  (`{eventId}|{timestamp}`); without a timestamp the RSVP applies to the occurrence in progress or
  the next one.

To add or change a command, edit `commands()` and the `command()` match, then run
`php artisan perscom:discord-register-commands`; Discord does not see changes until the commands
are registered again. Tests are in
`tests/Feature/Tenant/Http/Controllers/Integrations/DiscordInteractionsControllerTest.php`.

## Discord Role and Nickname Sync

`DiscordRoleSyncService` mirrors a member's rank and unit as Discord roles and sets their nickname
to rank abbreviation plus name, depending on the `sync_roles` and `sync_nicknames` toggles in
`discord_settings`.

- `UserObserver` dispatches `app/Jobs/Tenant/SyncDiscordMember.php` (after commit) when a linked
  member's `rank_id`, `unit_id`, `name`, or `discord_user_id` changes, and `ConnectedServices`
  queues one job per linked member when either toggle is first turned on.
- PERSCOM creates one role per rank and per unit and records it in `discord_settings.managed_roles`
  keyed `rank:{id}` or `unit:{id}`. Only roles in that map are ever removed from a member, so roles
  the server set up by hand are untouched.
- `ensureRole()` recreates a managed role deleted in Discord, under a cache lock per server and key
  so concurrent jobs do not create duplicates. It reuses an existing role by ID and never renames
  it, so renaming a rank or unit leaves the Discord role's old name in place.
- `ConnectDiscordServer` drops `managed_roles` when the organization switches servers, and
  `ConnectedServices` preserves the key when the settings form is saved.
- Discord refuses to rename the server owner or anyone above the bot's highest role, and
  `DiscordService::setNickname()` truncates to Discord's limit.

Covered by `tests/Feature/Tenant/Services/DiscordRoleSyncServiceTest.php`.

## Slack Notifications

Notifications build one Discord message in `toDiscord()`, and Slack reuses it. The
`NotificationChannel::SLACK` case maps to `app/Notifications/Channels/SlackPublicChannel.php`, which
converts the embed with `App\Support\Slack\SlackBlocks::fromDiscordEmbed()` (or the body alone when
there is no embed) and posts it with `SlackService::postMessage()` to the configured
`slack_channel`. A notification without `toDiscord()` is skipped, so adding Discord support to a
notification is what makes it available on Slack.

`SlackBlocks` maps the embed's author, title, description, fields (first 10), thumbnail, image, URL
(an "Open in PERSCOM" button), and footer onto Block Kit, truncating to Slack's limits. `markdown()`
escapes `&`, `<`, and `>` before converting `**bold**` and `[text](url)`, so text taken from
records cannot inject Slack mentions or links. Tests are in
`tests/Feature/Tenant/Notifications/Channels/SlackPublicChannelTest.php`.
