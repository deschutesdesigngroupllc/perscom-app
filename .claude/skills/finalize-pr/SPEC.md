# finalize-pr Specification

## Intent

Run the last pass over a feature branch before it becomes a PR, and produce one report a reader can
act on without redoing the work. The skill owns no conventions of its own: it discovers which rules,
skills, and docs govern the diff, invokes the checks that already exist, and spends its own effort
on what nothing else does: merging what several sources found into one prioritized, verified,
uniformly written list.

## Scope

In scope:

- Resolving one diff, working tree included, and cutting it into at most five areas
- The Pint and line-length gates inline; the PHPStan, Rector, and test gates inside the workflow
- A bug lens, a security-and-tenancy lens, and a simplifier, all propose-only
- Discovering and applying `.claude/rules/`, `.ai/rules/`, `CLAUDE.md`, and sibling skills
- A spec stage: the branch against its issue's acceptance criteria, the decisions posted after
  them, the readers of values it changes, and work no criterion asked for
- On a branch with an open PR, a ledger of its review comments, each marked fixed, replied,
  outstanding, disputed, or external, and a search of the branch for the pattern a comment names
- A review round: once someone has reviewed the PR, the scope starts at the reviewed commit
- Drafting replies to review comments into a scratchpad file
- Normalizing, deduping, rating, and verifying findings (the triage stage)
- Reporting them and asking how to proceed in one `AskUserQuestion`
- Applying fixes **after** the user selects them, through the fix workflow

Out of scope:

- Committing, pushing, opening, or editing a PR: `.claude/rules/git-workflow.md` owns these
- Posting anything to GitHub
- Inventing a convention. If a rule is not written down somewhere else, it is not a finding

## Users and Trigger Context

- **Primary users:** engineers on this repo finishing a branch
- **Common requests:** "finalize", "wrap up", "ready for review", "get this ready for a PR",
  `/finalize-pr`
- **Should not trigger for:** reviewing someone else's open PR (use `/code-review`), quality-only
  cleanup (use `/simplify`), or writing the PR body

## Runtime Contract

**Required first actions**

1. Resolve `BASE` against `origin/master` and bind the file list (`git diff $BASE` plus
   untracked); stop on `master`. In a review round, bind it against `SINCE`
2. Run Pint on exactly that list before anything reads a file, then measure added line widths
3. Gather the spec, asking when none is found
4. With an open PR, quote its unresolved threads and each reviewer's latest review into the ledger
5. Check for a concurrent test run and ask; the workflow cannot
6. Build the args, state the agent count, and launch

**Required outputs**

- A gate header where every gate reads `pass`, `fail`, or `unverified` with a reason
- A requirements table, or a coverage line saying the spec stage did not run
- With an open PR, a review comments table, and after a fix, `finalize-replies.md`
- One question offering fix everything, fix by area, or blockers only, with each `D` asked beside it
- Findings grouped by area, each with ID, severity, verification mark, and a fix
- An explicit statement of partial coverage wherever an agent was capped

**Non-negotiable constraints**

- Nothing is applied until the user selects it
- No commit, push, or GitHub write, ever
- Every `S1`, `S2`, and `D` is opened and checked by the triage agent before it is reported
- IDs are assigned once, in code, by `review.js`, never by an agent and never renumbered
- An unverified gate is never presented as a pass, nor as a failure of the branch

## Source and Evidence Model

Authoritative sources, in precedence order:

1. `.ai/rules/*.md` (path-scoped through `.ai/rules/index.md`) and `.claude/rules/*.md`
2. The nearest `CLAUDE.md`, walking up from each changed path, root included
3. Sibling skills whose description matches the diff
4. The branch's issues and every comment on them, for the spec stage only
5. The PR's review threads and reviews, for the ledger only. A comment is checked against the code
   before it is agreed with, and one that would break a rule above is disputed

## Reference Architecture

- `SKILL.md`: the inline steps (scope, preflight, report, ask) and the two launches
- `workflows/review.js`: gates, producers, triage, and ID assignment; its header documents `args`
- `workflows/fix.js`: file-disjoint grouping, ordered application, Pint, and selective re-gate
- `references/report.md`: rendering rules, needed only once the review returns
- `references/replies.md`: how a reply to a review comment reads, needed only after a fix
- `SPEC.md`: this contract

## Validation

- **Lightweight:** both scripts parse (strip `export`, wrap the body in an async function,
  `node --check`), and a stub harness that fakes `agent()` runs each end to end
- **Deeper:** run the skill on a real branch and check the report against this contract
- **Acceptance gate:** a reader who did not watch the run can pick a fix option and start work
  without asking a clarifying question

## Known Limitations

- `S3` and `S4` findings are single-source unless the triage agent happened to confirm them.
- The bug and security lenses read the whole diff each; past about 200 files they sample, and their
  `notReached` lists are the only record of what they skipped.
- On `develop` with no open PR, the diff against `master` can cover many finished features; the
  skill asks to narrow it with `since <ref>`.
- Coverage on very large diffs is sampling, not sweeping.

## Maintenance Notes

- Update a workflow script when a gate's failure mode changes or a finding source is added,
  `references/report.md` when a report rule fails a reader, and `SKILL.md` when an inline step
  changes.
- Update `SPEC.md` when scope, the runtime contract, or the validation gates change.
