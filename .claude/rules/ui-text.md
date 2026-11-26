# UI Text

How the words in the panels read: who they are written for, and how they are cased and punctuated.
Applies to every label, heading, button, placeholder, helper, notification, empty state, and
validation message. Strings are plain English literals, never `__()` (see `.ai/rules/app.md`).

## Write for the Member

UI text is read by unit leaders, administrators, and members of the organization, not by the
people who built the feature. Name things by what the member is doing and seeing, never by how the
system models it.

| Engineer-speak     | Member copy        |
|--------------------|--------------------|
| Newsfeed Item      | News Post          |
| Occurrence         | Date               |
| Registration       | RSVP               |
| Causer             | Author             |

- Military and organizational terms are fine where the organization uses them (Rank, Unit,
  Assignment). Model, column, enum, and class names never appear.
- An action keeps one name through the whole flow: a button that says "Reset Link" produces a
  notification that says the link was reset.
- An error says what went wrong and how to fix it. An empty state says how the first item arrives.
- Labels are never uppercase. A small uppercase eyebrow above a widget group is the one exception.

## Title Case

Labels, headings, buttons, tabs, and modal headings are Title Case: capitalize every word except
articles (`a`, `an`, `the`), coordinating conjunctions (`and`, `but`, `or`, `nor`), and short
prepositions (`as`, `at`, `by`, `for`, `in`, `of`, `on`, `to`, `via`, `with`), unless the word
opens or closes the label.

| Wrong                   | Right                   |
|-------------------------|-------------------------|
| Time in grade           | Time in Grade           |
| Registration enabled    | Registration Enabled    |
| Mark as unread          | Mark as Unread          |
| Sent at                 | Sent At                 |

Acronyms stay uppercase: API, ID, URL, RSVP, MFA, SMS, MCP, RSS.

### Generated Labels

`App\Support\Label` gives every field, column, entry, and filter without an explicit label a Title
Case default derived from its name, registered in `AppServiceProvider`:

- `first_name` becomes "First Name", and `api_key` becomes "API Key".
- A relationship field is named after its relationship, and a foreign key drops its `_id`:
  `author_id` becomes "Author".

Add `->label()` only when the derived label is wrong or unclear, and write it in Title Case.

## Element Reference

| Element                        | Casing        | Period | Example                                          |
|--------------------------------|---------------|--------|--------------------------------------------------|
| Navigation label               | Title Case    | No     | Connect AI Tools                                 |
| Page title                     | Title Case    | No     | Edit Event                                       |
| Section heading                | Title Case    | No     | Recent Announcements                             |
| Field, column, or entry label  | Title Case    | No     | Minimum Days in Grade                            |
| Tab label                      | Title Case    | No     | Who's Going                                      |
| Action button                  | Title Case    | No     | Reset Link                                       |
| Modal heading                  | Title Case    | No     | Subscribe to Calendar                            |
| Modal description              | Sentence case | Yes    | Your current links will stop working.            |
| Helper text                    | Sentence case | Yes    | The author of the record.                        |
| Placeholder                    | Sentence case | No     | Search members                                   |
| Notification title, with body  | Title Case    | No     | Link Reset                                       |
| Notification title, alone      | Sentence case | Yes    | Your calendar link has been reset.               |
| Notification body              | Sentence case | Yes    | Calendar apps must subscribe again.              |
| Empty state heading            | Sentence case | No     | No announcements yet                             |
| Empty state description        | Sentence case | Yes    | Create an event to get started.                  |
| Validation message             | Sentence case | Yes    | The :attribute field is required.                |

### Placeholders

Give a placeholder to any field, column, or entry whose value may be missing, so a blank never
reads as broken: `->placeholder('None')` on a nullable column or entry, and an instruction on an
optional input.

### Helper Text

Every resource form field carries helper text (see `.ai/rules/resources.md`): one sentence saying
what the value is for, ending in a period.

### Notifications

A notification with a body has a short Title Case title and a sentence body. A notification with
no body is a single sentence ending in a period.

### Empty States

The heading is short with no period. The description is one sentence saying how to get a first
item, ending in a period.
