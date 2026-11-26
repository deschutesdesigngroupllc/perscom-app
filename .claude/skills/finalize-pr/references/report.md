# Rendering the Report

The review workflow has already done the normalization: duplicates merged, severity set on one
scale, every S1, S2, and D checked by the triage agent, and IDs assigned in their final order. This
file covers only how to turn its return value into the report. Two failure modes make a correct
report useless, and both come from relaying raw material as-is: **grouping by the tool that found a
finding**, so a reader fixing one area visits four sections, and **relaying each source's own
shape**, so nothing compares down a column.

## Order: verdict, gates, requirements, review comments, findings

A reader opens this to decide what to do. The findings are evidence for that decision, so they go
last. No preamble before the first line.

````markdown
**12 findings** — S1 1 · S2 2 · S3 7 · S4 2 · D 0    **Blocks merge: F1, F2, F3**

| Gate        | Verdict      | Note                                                     |
|-------------|--------------|----------------------------------------------------------|
| Pint        | ✓ pass       | 1 file rewritten                                         |
| PHPStan     | ✓ pass       |                                                          |
| Rector      | ✗ fail       | 2 rules — see F9                                         |
| Tests       | ⚠ unverified | Collided with a concurrent run — not evidence either way |
| Line length | ✗ fail       | F8 — 2 files                                             |

**Coverage:** area `app/Filament` hit its cap — lower-ranked findings not returned.

| Criterion                                              | Status    | Proven by                                           | Findings |
|--------------------------------------------------------|-----------|-----------------------------------------------------|----------|
| **C1** #NNN · Members can open event detail pages      | ✓ met     | `EventResourceViewTest::test_members_with_the_def…` |          |
| **C2** #NNN · Members do not see the admin event list  | ✗ missing |                                                     | F2       |

---

### Security & tenancy

| ID     | Sev  | Location                  | What breaks                                          | Fix                                       | Src |
|--------|------|---------------------------|------------------------------------------------------|-------------------------------------------|-----|
| **F1** | S1 ✓ | `routes/app.php:43`       | Any token holder can read another tenant's RSS feed  | Resolve the feed user on the tenant DB    | sec |
````

Then close with a one-line `Src` legend and the question in the skill's step 4. The question *is*
the menu; do not also print one.

## Gates

Gates are status, not findings: the header table and nowhere else. Pint comes from the preflight;
the rest come from the workflow's `gates`.

| Verdict        | Means                                  |
|----------------|----------------------------------------|
| **pass**       | It ran clean                           |
| **fail**       | It ran, and the branch broke it        |
| **unverified** | It never produced a trustworthy result |

A gate is unverified when it collided with another run, ran out of memory, timed out, or was
skipped. An unverified gate never reads as a pass, and never as a failure of the branch.

## Coverage

Print every `coverage` line the workflow returned, under the gate table. A capped area or an
unreached file left unsaid turns a sample into a completeness claim the run did not earn. Omit the
line only when `coverage` is empty.

## Requirements

When the workflow returns `requirements`, print them as one table under the coverage line, one row
per criterion in `C` order, every criterion included. When it is `null`, print nothing here; the
coverage line already says the spec stage did not run.

| Column      | Holds                | Rules                                                             |
|-------------|----------------------|-------------------------------------------------------------------|
| `Criterion` | `**C2** #NNN · text` | Bold ID, issue, then the text cut at about 70 characters with `…` |
| `Status`    | `✓ met`              | `✓ met`, `⚠ untested`, `◐ partial`, `✗ missing`, or `? unclear`   |
| `Proven by` | `` `Test::method` `` | The first test, then `+n`. Empty unless met                       |
| `Findings`  | `F6, F9`             | The row's `findings`, so a reader can jump to the fix             |

Append `(derived)` to a criterion's text when it is.

## Review Comments

When the workflow returns `reviewItems`, print them as one table after the requirements, one row
per `R` item in order, every item included, under a count line:
`**Review comments:** 9 — 5 fixed · 1 replied · 2 outstanding · 1 external`.

| Column     | Holds              | Rules                                                                  |
|------------|--------------------|------------------------------------------------------------------------|
| `Item`     | `**R7** Blocking`  | Bold ID; `Blocking` when it is                                         |
| `Where`    | `` `Foo.php:62` `` | The `where`, as a clickable path, or `review` or `PR body`             |
| `Status`   | `✓ fixed`          | `✓ fixed`, `↩ replied`, `✗ outstanding`, `⚖ disputed`, or `⏸ external` |
| `Evidence` | `evidence`         | One clause: the place a fix landed, or what is missing                 |
| `Findings` | `F3`               | The row's `findings`                                                   |

## Findings

One table per area, in the workflow's order, which is already the priority: Security & tenancy,
Data & schema, Correctness, Spec & requirements, Review comments, Conventions, Docs & companions,
and Quality. Drop an empty area rather than printing its heading.

| Column        | Holds                                                                  |
|---------------|------------------------------------------------------------------------|
| `ID`          | `**F7**`                                                               |
| `Sev`         | `S1 ✓`                                                                 |
| `Location`    | `` `path/to/File.php:41` ``                                            |
| `What breaks` | `consequence`                                                          |
| `Fix`         | `fix`                                                                  |
| `Src`         | `bug`, `sec`, `spec`, `pr`, `rules`, `len` (measured width), or `simp` |

- **`ID`:** bold, exactly as the workflow assigned it, never renumbered.
- **`Sev`:** `✓` when `verified`, `?` when not.
- **`Location`:** the full path so it stays clickable. Elide the middle of a deep path with `…`,
  never the ends. Rolled-up findings show the count: `` `Foo.php:12` +8 ``.
- **`What breaks`:** one clause, no trailing period.
- **`Fix`:** an imperative phrase.
- **`Src`:** joined with `+` when merged.

`?` is honest: the finding came from one source and nobody re-opened the file. Never promote a `?`
to `✓` without opening the file yourself, and never drop a `?` to avoid the caveat.

Keep each row to two terminal lines. A finding outside the diff is marked `(outside diff)` in
`What breaks`, sits last in its area, and never counts as a blocker. No code blocks in a cell.

## The Table That Survives Compaction

Write the rendered findings tables to the scratchpad (`finalize-findings.md`) before asking the
question. A finalize pass spans many turns; the fix step and any re-report read IDs from that file,
not from memory.

## A Clean Branch

When `findings` is empty, report the gate table and "No findings." in two lines. Skip the question;
there is nothing to choose.

## After a Fix Run

Re-print only the findings that were selected, adding an `Outcome` column (`fixed`, `skipped` with
why, or `no_change_needed`), then the new gate table. Same IDs.
