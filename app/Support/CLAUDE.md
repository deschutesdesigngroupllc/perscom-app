# Support

`app/Support/` holds small shared helpers that are neither models nor services, plus adapters for
third-party packages in their own subdirectories (`Passport/`, `Tenancy/`, `Twig/`, and so on).
This file covers the presentation helpers at the top level and in `ICalendar/`: `Label` for
generated UI labels, `Brand` for the color palette, and the iCalendar writer.

## Generated Labels

`Label` turns an attribute name into a Title Case label. Nothing calls it from a resource
directly: `AppServiceProvider::configureTitleCaseLabels()` registers `configureUsing()` callbacks
on the base classes `Field`, `Entry`, `Column`, and `BaseFilter`, so every component without its
own label gets one from `Label::fromName()`.

- A field with a relationship is named after the first segment of the relationship
  (`position.unit` gives "Position"). Otherwise the last dotted segment of the name is used.
- A column is named after the segment before its final attribute (`rank.name` gives "Rank").
- `fromName()` drops a trailing `_id` or `Id` before converting, so `author_id` gives "Author",
  while a bare `id` gives "ID".

The callbacks are registered on base classes and before the per-name blocks in
`AppServiceProvider::boot()`, so a subclass `setUp()` label, a per-name block (`created_at`
becomes "Created"), and an explicit `->label()` all override the generated one. Add `->label()`
only when the derived label is wrong.

`titleCase()` only raises the first letter of each word with `Str::ucfirst()` and never lowercases
the rest, so "RSVP Deadline" and mixed-case input survive. A word in `MINOR_WORDS` is lowercased
unless it opens or closes the label, and a word in `ACRONYMS` is always written in its listed
form, even at the edges.

To teach it a new word:

- Add the acronym to `ACRONYMS` keyed by its lowercase form. Plurals are separate entries (`ids`
  and `urls` are listed beside `id` and `url`).
- Add a short preposition or conjunction to `MINOR_WORDS`.
- Add a case to `tests/Unit/Support/LabelTest.php`, and keep the lists in
  `.claude/rules/ui-text.md` (Title Case) in step, since that rule is what people follow when they
  write a label by hand.

## Brand Colors

`Brand` is where the Filament palette and the brand hex values live. `Brand::colors()` maps the
semantic names `primary`, `gray`, `info`, `success`, `warning`, `danger`, and `brass` to Filament
color ramps. `warning` and `brass` are built from the same `ACCENT_HEX`, so attention states and
insignia share one gold.

`colors()` is registered in several places, and each one is needed for the brand to show:

| Where                                    | Covers                              |
|------------------------------------------|-------------------------------------|
| `AdminPanelProvider`, `AppPanelProvider` | The admin and app panels            |
| `PublicPanelProvider`                    | The public panel                    |
| `AppServiceProvider::boot()`             | Pages outside a panel (invitations) |
| `ServiceProfiles\ShowController`         | Public service profiles             |
| `Api\Widgets\WidgetController`           | Embeddable website widgets          |

Without a registration Filament falls back to its default amber primary;
`tests/Feature/Tenant/Providers/BrandColorsTest.php` checks the invitation page for brand blue.

Registering `brass` makes Filament emit `--brass-*` CSS variables, which
`resources/css/filament/app/theme.css` maps to Tailwind `brass-*` utilities. A `text-brass-700`
class in Blade therefore only renders on a page where `colors()` was registered and that theme is
loaded.

The constants serve code that needs a raw hex rather than a ramp:

- `PRIMARY_HEX` is the fallback for colors a user picks (calendars, announcements, banners, and
  the `HasColorField` trait), the Discord bot's embed color in `DiscordInteractionService`, the
  service profile card image, and the admin chart widgets.
- `NEUTRAL_HEX` is the fallback when a status has no color, in resources, relation managers, and
  the roster and profile Blade views.

Usage follows `.claude/rules/design-patterns.md`: components take a semantic name, never a hex;
`primary` is for actions and never a status; `brass` marks insignia moments (ranks, awards,
promotion eligibility, and "New" or "Latest" badges).

### Copies Outside `Brand`

`Brand` is not the only place the hex values are written. Changing the brand means updating these
by hand as well:

- `resources/css/landing/app.css` and `resources/js/landing/components/Logo.jsx`
- `resources/views/vendor/mail/html/themes/default.css` (the mail theme)
- `resources/views/errors/layout.blade.php` and `resources/views/components/logo.blade.php`
- `resources/js/filament/app/app.jsx`
- The ui-avatars URL in `app/Traits/HasProfilePhoto.php`

`CATEGORY_COLORS` and `categoryColor()` have no callers. The record notifications under
`app/Notifications/Tenant/` set their own hex in `toDiscord()`, and those values differ from
`CATEGORY_COLORS`.

## iCalendar Writer

`ICalendar/Component` builds an RFC 5545 component as a list of content lines plus nested
components, and `ICalendar/Calendar` is the `VCALENDAR` wrapper whose `create()` writes the
publishing headers (`PRODID`, `METHOD:PUBLISH`, `X-WR-CALNAME`, `X-WR-TIMEZONE`, and a one-hour
refresh hint). The only caller is `App\Services\CalendarFeedService`, which builds the `VEVENT`s
for subscription feeds and single-event downloads; the routes live under `feeds/{token}` in
`routes/app.php` and in `Events\CalendarController`.

The builder methods differ in how much they format:

- `property()` writes the value exactly as given. Use it only for values already in iCalendar
  form (`UID`, `RRULE`, `EXDATE`, `STATUS`).
- `text()` escapes backslashes, semicolons, commas, and newlines, and skips a blank value
  entirely. Any free text (names, descriptions, locations) must go through `text()`, or a comma
  in an event name corrupts the line.
- `dateTime()` writes UTC with a `Z` suffix when no timezone is given, otherwise local time with a
  `TZID` parameter. `date()` writes `VALUE=DATE` for all-day values.
- Parameter values containing `:`, `;`, or `,` are double-quoted with any `"` removed, and blank
  parameter values are dropped.

`lines()` returns the unfolded lines, which is what to assert against in a test. `render()` folds
each line at 75 octets, counting bytes but never splitting a multibyte character, and ends every
line with CRLF, including the last. `make()` uppercases the component name; the constructor does
not.

No `VTIMEZONE` component is written. A `TZID` names an IANA zone, always the organization's
`ScheduleService::timezone()`, with no definition in the document. `CalendarFeedService` writes
one-time events in UTC and recurring series in local time with `TZID` so the series keeps its
wall-clock time across daylight saving changes.

The static formatters (`formatDate()`, `formatUtcDateTime()`, and `formatLocalDateTime()`) are
also used outside a component, for Google Calendar template links and `EXDATE` lists.

Tests: `tests/Feature/Tenant/Support/ICalendar/ComponentTest.php` covers escaping, folding, and
rendering; feed content is covered by `tests/Feature/Tenant/Services/CalendarFeedServiceTest.php`
and the controller tests under `tests/Feature/Tenant/Http/Controllers/App/Calendars/`.
