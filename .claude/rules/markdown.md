# Markdown

Applies to every `CLAUDE.md`, every file under `.claude/rules/` and `.claude/skills/`, and every
file under `.ai/rules/`.

## Wrap Prose at 100 Columns

Hard-wrap each paragraph so no line passes 100 columns, breaking on word boundaries and never
inside a backtick span. Headings, table rows, and code fences are not wrapped.

## Use the Oxford Comma

A list of three or more items takes a comma before the final `and` or `or`, in prose and in table
cells alike.

```
BAD:  `blank()`, `filled()` and `isEmpty()`
GOOD: `blank()`, `filled()`, and `isEmpty()`
```

## Align Tables

A table row stays on one line, and its columns line up in the source:

- Pad every cell with trailing spaces to the width of the widest cell in its column.
- Keep one space between a cell and each pipe.
- Fill the separator row with dashes to the full width of the column, with no colons.

Adding a row wider than its column means padding every other row to match.

## Keep Tables Glanceable

A table holds what a reader takes in at a glance: names, values, and short facts to compare down a
column. When a cell has to be read like a sentence, or one row is far wider than the rest, keep
the fact in the cell and move the explanation to a paragraph or list under the table.

## State What Is True Now

A rule or `CLAUDE.md` describes the code as it is. It never carries an issue or PR number, the
history of a change, or an open question; those belong on the issue or the PR.
