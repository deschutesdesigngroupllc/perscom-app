---
name: docblocks
description: "Use when writing, editing, or reviewing PHPDoc docblocks on PHP classes, methods, properties, or constants — e.g. adding a title to a new method, deciding whether a docblock is needed at all, or trimming redundant @param/@return tags. Codifies this repo's docblock cadence: one-line titles on every method docblock, title-and-tags docblocks in laravel/framework's style, tag descriptions without trailing periods, imported class names over FQCNs, and type tags kept only where they add something the typed signature doesn't. Do not use for inline // comments, Blade, or non-PHP docs."
version: 1.0.0
---

# Docblocks

A docblock is hover-height documentation for the next developer reading the symbol in their
editor. It states **what the thing does and what a caller must know**, in the present tense, and
nothing else. This repo runs strict typed signatures, so a docblock never restates a type the
signature already carries; it earns its place by saying something the signature can't.

This skill is the judgment guide. The mechanical rules it builds on are authoritative in
`.claude/rules/coding-standards.md` (Docblocks, Exceptions) and `.claude/rules/spelling.md`. When
this skill and a rule disagree, the rule wins and this file should be corrected.

## The Bar

Before writing a docblock, clear these in order:

1. **Does it need one at all?** If the name plus the typed signature already say everything, write
   nothing. A redundant docblock is a second thing to keep in sync, which is worse than none.
2. **Is this a class?** A class, trait, interface, or enum docblock exists only to carry a tag the
   declaration cannot express. See **Class Docblocks**.
3. **Title, then one fact at most.** A method docblock is its title and its tags. A short
   paragraph is allowed only for one fact a caller would otherwise trip on. If the method needs
   more, extract or refactor it.

## Method Docblocks

**A method docblock always opens with a one-line title.** A block that is only `@param`,
`@return`, or `@throws` is incomplete: give it a title or delete it.

Three carve-outs where a title-less block is correct:

- **Constructors** carrying only an array-shape or generic `@param`, or a `@throws`.
- **Framework lifecycle and hook methods**: `handle()` on a job, `boot()`, `booted()`,
  `register()`, `mount()`, `setUp()`, `tearDown()`, and overrides of a framework parent such as a
  resource's `form()`, `table()`, or `getPages()`. A title adds nothing to these; keep a
  title-less block when it carries a generic or a `@throws`. A domain method that merely starts
  with a framework verb (`handleAttendance()`) is not a lifecycle method and keeps its title.
- **Generated model `@property` blocks**, written by `composer ide`. Never hand-write or trim
  them; regenerate them, and add a PHPStan ignore when the generated block is wrong.

A title is:

- **Imperative, verb-first, one physical line, ending in a period**, within 80 columns:
  `Resolve…`, `Build…`, `Grant…`, `Get…`. If it wants to wrap, the method is doing too much or
  the title is smuggling in a description.
- **Self-contained.** It reads from the signature alone. It doesn't name the caller, a sibling
  method, a ticket, or UI the hover can't see.
- **Abstract.** Naming a column, a sibling `method()`, or a concrete class is usually leaking
  implementation detail. When a code token is essential, backtick it so hovers render it as code.

```php
// GOOD — verb-first, one line, self-contained
/**
 * Resolve the member's private feed token.
 */
public static function tokenFor(User $user): string

// BAD — only tags, no title
/**
 * @return BelongsToMany<User, $this>
 */
public function registrations(): BelongsToMany

// BAD — a description masquerading as a title, wrapping past 80 columns
/**
 * Look up the occurrence the member picked, falling back to the nearest one, and cache it for the page.
 */
public function getSelectedOccurrence(): ?OccurrenceData
```

### One Caller-Facing Fact

Most methods stop at the title. Add one short paragraph only for something the signature can't
convey and a caller would trip on: a side effect, an edge case, or a precondition.

```php
/**
 * Replace the member's feed token.
 *
 * Every calendar and RSS link issued before stops working.
 */
public static function resetToken(User $user): string
```

The paragraph never says what the method does (the title does) or why it was built that way. If
it needs more than a sentence or two, the method is doing too much.

## Type Tags

A tag that echoes the typed signature (`@param string $name`, `@return Foo`) is banned
redundancy. Keep a tag only when it adds information the type can't:

- collection and iterable **generics**: `@return Collection<int, Event>`
- **array shapes**: `@param  array{width: int, height: int}  $size`
- **narrowing** a broad hint: `array` to `list<string>`, `string` to `class-string<Model>`
- **`@throws`**, a caller-facing contract

Use imported class names, never FQCNs (`@mixin` and `@see` may use one). Tag descriptions take no
trailing period; only the title does. Keep `@return $this` on a fluent method. An array shape with
more than a couple of keys, repeated across signatures, becomes a `Data` class.

## `@throws`

List every exception a method can let escape: the ones it throws, the implicit ones
(`UnhandledMatchError` from a non-exhaustive `match`, `JsonException` from `JSON_THROW_ON_ERROR`),
and the ones a call it makes declares and it does not catch. Describe each as a `when …` clause.

```php
/**
 * Encode the feed payload.
 *
 * @throws JsonException  when the payload holds a value JSON cannot represent
 */
```

Propagation, JSON, and custom exceptions are covered in `coding-standards.md` under Exceptions.

## Class Docblocks

`laravel/framework` gives its classes no summary: the name and the members say what a class is.
A class, trait, interface, or enum docblock exists only to carry a tag the declaration cannot
express: `@mixin`, `@template`, `@implements`, `@extends`, `@property`, `@method`,
`@phpstan-type`, or `@phpstan-require-extends`.

- A new class gets no summary or description. What it is for, and the invariants callers rely on,
  go in the nearest `CLAUDE.md` when they need writing down at all.
- An existing prose class docblock is trimmed when a change touches the class, not rewritten in
  passing.

```php
// GOOD — a tag the declaration cannot say
/**
 * @mixin Eloquent
 */
trait HasDocument

// GOOD — nothing to add, so no docblock
class RssFeedService

// BAD — prose that belongs in CLAUDE.md, if anywhere
/**
 * Private RSS feeds of the organization's announcements and newsfeed.
 */
class RssFeedService
```

## Constructors and Promoted Properties

A constructor rarely needs a docblock; the promoted signature states the shape. One that exists
only to carry an array-shape or generic `@param`, or a `@throws`, omits the title. Document a
promoted property on the constructor's `@param`, never again on the property.

## Properties and Constants

Same test as methods: a docblock only when it adds a generic, an array shape, or a caveat the name
and type can't carry. Declarations always use the multi-line form, even for a lone `@var`.

```php
// GOOD
/**
 * @var Collection<int, OccurrenceData>|null
 */
protected ?Collection $occurrences = null;

// BAD — single-line form on a declaration
/** @var Collection<int, OccurrenceData>|null */
protected ?Collection $occurrences = null;

// GOOD — the unit is a caveat the name doesn't carry
/**
 * How far back subscription feeds reach, in days.
 */
public const int FEED_HISTORY_DAYS = 90;
```

## Inline Annotations

The single-line `/** … */` form is only for an annotation on the next statement inside a method
body: a local `@var` narrowing or a PHPStan suppression.

```php
/** @var User $user */
$user = Auth::user();
```

## Inherited Docblocks

When a method overrides a parent or implements an interface method that already has a docblock,
write none, and don't reach for `{@inheritDoc}`; tools inherit the parent's block. The exception
is `@throws`: it goes on the interface or parent method, because callers reach it through that type.

## Keep Out of Docblocks

- Design rationale: why it was built this way.
- Change history, ticket numbers, PR links, plans, TODOs, or reasoning from a working session.
- `@author`, `@since`, and `@version`; git owns them.

## Match the Code's Abstraction Level

A docblock describes the contract at exactly the generality the code has. When a change widens
what the code handles, widen the docblock: a method that once handled profile fields and now
handles any payload is no longer "Prefill blank profile fields". Never name today's consumers
("used by the dashboard and the calendar"); the contract doesn't depend on who calls it.

## Checklist

- [ ] Would the name and typed signature alone be enough? Then no docblock.
- [ ] A class? No docblock unless it carries a tag.
- [ ] Every method docblock opens with a one-line, verb-first title ending in a period, within 80
      columns, except the carve-outs.
- [ ] One caller-facing fact at most after the title.
- [ ] `@param` and `@return` only for generics, array shapes, or narrowing.
- [ ] `@throws` for every exception the method lets escape, phrased as a `when …` clause.
- [ ] No trailing period on tag descriptions; imported class names, not FQCNs.
- [ ] Multi-line form on every declaration.
- [ ] Overriding a documented method? No docblock.
- [ ] No history, tickets, plans, TODOs, or `@author`.
- [ ] American English; run `vendor/bin/pint --dirty --format agent` when done.
