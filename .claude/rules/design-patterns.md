# Design Patterns

How Filament components are chosen, composed, and configured in the panels: sections, forms,
modals, tables, widgets, and color. UI text (casing, placeholders, copy) lives in `ui-text.md`.
Short area rules (badges, modal headers, copyable values, icons) live in `.ai/rules/filament.md`.

## Sections

- A page or widget card is a full-size section. `compact` is only for a section nested inside
  another, a tile in a grid, or a chart node, never for a card that sits beside full-size ones,
  whose gutter would then read narrower than its neighbors.
- **No card in a card.** A section that only groups other sections is `->contained(false)`.
- A container never renders empty. When every child can be hidden (all tabs of a `Tabs`, every
  field of a `Section`), the container takes the same visibility condition. Tabs already hide
  themselves when every tab is hidden.
- A section's icon and description describe it at a glance; keep the description to one line.

## Field Placement

- At most two or three columns per section.
- Place tightly coupled fields side by side (a start and end date, an amount and its unit), and
  everything else in a single column.
- Reveal fields progressively: a field that only matters after an earlier answer stays hidden
  until that answer is given.
- Inline labels (`->inlineLabel()`) sit level with the top of their control. The theme aligns
  them; never center a label against helper text.

## Form Methods

| Method                     | When to use                                                         |
|----------------------------|---------------------------------------------------------------------|
| **Wizard**                 | Ordered steps where later inputs depend on earlier ones             |
| **Progressive disclosure** | One form whose inputs are mostly independent, with shallow branches |
| **Simple form**            | A few independent fields where speed matters most                   |

A setup flow that gates later steps on earlier ones (onboarding, the organization builder) is a
wizard. Everything else is a single form that hides what does not apply.

### Section Dependencies

When one part of a form cannot be filled in until another is complete, disable it with a tooltip
naming the prerequisite ("Add a unit before assigning positions."), rather than letting the user
in and failing on save.

## Modals and Slide-Overs

A custom action that opens entry UI picks its container by size. A resource's or relation
manager's create and edit actions keep Filament's default.

| Entry                                                              | Use        |
|--------------------------------------------------------------------|------------|
| A confirmation, or one to three flat fields                        | Modal      |
| Four or more fields, sub-steps, or a record created from elsewhere | Slide-over |
| A read-only detail (a post, an occurrence)                         | Modal      |

- A modal has a Title Case `->modalHeading()` and a sentence `->modalDescription()`; see
  `.ai/rules/filament.md` for when it may carry an icon.
- A modal that only shows information sets `->modalSubmitAction(false)` and labels its cancel
  button `Close`.
- A slide-over may open one nested slide-over, never more.

## Quick-Select Inputs

Prefer a button group over a dropdown when the option set is small and the labels are short.

| Field shape                                  | Use                       |
|----------------------------------------------|---------------------------|
| Yes or no                                    | `Toggle`                  |
| Two to five short values                     | `ToggleButtons`           |
| Options that each need a sentence to explain | `Radio` with descriptions |
| Six or more options, or searchable           | `Select`                  |

A button label is a value, not a sentence. When an option needs explaining, it is a `Radio` with
descriptions, and the enum supplies them through `getDescription()` with
`Filament\Support\Contracts\HasDescription`. Keep descriptions static: state the rule an option
applies, not a value computed from live form state.

## Tables

- Every table that can be empty has an empty state heading, description, and icon naming what it
  holds (see `.ai/rules/resources.md` for resource tables).
- A list page states what it lists in its subheading.

## Tabs

Every tab carries an icon, and a tab set never mixes tabs with icons and tabs without them.

## Dashboard Widgets

A dashboard widget that shows more than a table is a custom Blade view inside an
`<x-filament::section>`:

- The section takes an outlined Heroicon, a Title Case heading, and a one-line description. Its
  icon color says what it holds: `primary` for a feed of posts or events, `brass` for insignia and
  setup prompts (rank, the launch plan, items needing attention), and the record kind's own color
  otherwise.
- Its own action is a link: in the section footer for "View All …" or "Open Calendar", or in
  `afterHeader` for a small control such as "Hide" or "RSS". Buttons are for actions inside the
  card.
- An item opens its detail in a modal mounted on the widget, through `HasActions` and
  `<x-filament-actions::modals />`.
- An empty widget uses `<x-filament::empty-state>` with `:contained="false"`, saying how the first
  item will arrive.
- Rows that change on hover change their text color, not a background that crosses the dividers.

## Color

- `->color()` takes a semantic name: `primary`, `gray`, `success`, `warning`, `danger`, `info`,
  or `brass`. Never hardcode a hex on a component.
- Brand blue (`primary`) is for actions, never for a status. Brass is the accent for insignia
  moments: ranks, awards, promotions, and "Latest" or "New" markers. See `App\Support\Brand`.
- A color the user chose (a calendar's, an announcement's) may be applied inline as the accent it
  was chosen for.
- An ordinary value takes no color. Return `null` from a `->color()` closure for the ordinary case,
  not `'gray'`, which makes the value recede behind its label. Only a status or a negative earns a
  color.

## Styling

In PHP schemas, style a component through Filament's own methods (`->color()`, `->weight()`,
`->size()`, `->fontFamily()`, `->alignEnd()`), not visual utilities passed through
`extraAttributes()`. A custom Blade view uses Filament's Blade components where one exists and
Tailwind utilities, with a `dark:` variant for every color, where none does. A rule that changes
how a Filament component renders everywhere goes in `resources/css/filament/app/theme.css`.

Verify a UI change in the browser at the widths it is used before calling it done.
