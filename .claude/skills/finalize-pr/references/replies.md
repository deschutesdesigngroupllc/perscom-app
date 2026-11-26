# Drafting Review Replies

The ledger says which review comments a round settled. These drafts are how the reviewer
learns it without re-reading the diff. Nothing here is posted: the user reads the file and
posts what they choose.

## Before Writing One

Check the comment against the code before agreeing with it. A reviewer reads the diff the
way any reviewer does and can be wrong: a cited line that has moved, a rule applied outside
its scope, a case the code already handles. A reply that agrees with a wrong comment costs
a round, the same as one that ignores a right one.

## By State

| Item ended as          | The reply says                                                    |
|------------------------|-------------------------------------------------------------------|
| Fixed                  | What changed and where, in one sentence                           |
| Pattern fixed too      | The same, plus the other places it was fixed                      |
| Left as it is          | Why, citing the rule, criterion, or decision it rests on          |
| Disputed               | What acting on it would break, and the alternative, if one exists |
| External               | What is needed, and from whom                                     |
| A point about the body | The replacement text, ready to paste                              |

## How It Reads

- Plain and short: one to three sentences for a thread, a numbered list for a review's
  points, matching its numbers.
- No thanks, apologies, or praise. "Fixed: the title now fits on one line" is the whole
  reply.
- Present tense, about the code as it stands. No account of how the fix was found.
- Name places as `file:line`, never as a pasted diff.
- A fix not yet committed says so — "Fixed in the next push" — rather than naming a commit.

## Layout of the File

```markdown
## R1 · thread · `path/to/File.php:62`

Fixed: <what the code does now, in one sentence>. Covered by `<Test>::<method>`.

## R7 · review, Blocking 3

Needs <who>: <what, stated so they can act on it without reading the thread>.
```

Thread replies go in their thread; a review's points go together in one PR comment, in the
reviewer's order.
