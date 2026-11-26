---
name: finalize-pr
description: "Final review-and-fix pass over a feature branch: runs the gates, review lenses, repo rules, the issue's spec, and the PR's review comments in a read-only workflow, reports one verified list, and applies only the fixes the user picks. On a reviewed PR it scopes to the work since the review and drafts the replies. Use when the user says finalize, wrap up, ready for review, pre-PR check, or address the review, or invokes /finalize-pr. Stops before committing or pushing."
user-invocable: true
version: 1.0.0
---

# Finalize a PR

This skill owns no rules. Every convention it checks is written down somewhere else:
`.claude/rules/`, `.ai/rules/`, a `CLAUDE.md`, or a sibling skill. Its job is to find the ones
that govern this diff, hand them to reviewers that read them in full, and turn what comes back
into one report the user can act on without redoing the work.

```
1. Scope       inline   one diff, working tree included, bound once — a review round starts at the review
2. Preflight   inline   Pint, line length, spec, review comments, collision check, areas, sources, args — then state the cost
3. Review      workflow workflows/review.js — gates ‖ lenses ‖ spec ‖ ledger ‖ areas → triage → IDs
4. Report      inline   references/report.md, then one question: how to proceed
5. Fix         workflow workflows/fix.js — only what the user picked, then re-gate
```

Everything that needs the user (a running test suite to ask about, a decision only they can make,
which fixes to take) happens inline. Everything that only needs reading happens in a workflow, so
PHPStan output and twenty files of rule text never land in this context.

It ends before the commit. `.claude/rules/git-workflow.md` owns commits and the PR body.

## 1 — Scope

```bash
git rev-parse --abbrev-ref HEAD
BASE=$(git merge-base --fork-point origin/master HEAD || git merge-base origin/master HEAD); echo "BASE=$BASE"
{ git diff --name-only --diff-filter=d "$BASE"; git ls-files --others --exclude-standard; } | sort -u > <scratchpad>/finalize-files
wc -l < <scratchpad>/finalize-files
```

On `master`, say so and stop. On `develop` with no open PR, the diff against `master` can span
many finished features: report the file count and ask whether to narrow it with `since <ref>`
before going further.

Diff against `$BASE` with **no `...HEAD`**. The three-dot form compares commits only, so
uncommitted edits, often the newest and least-reviewed work on the branch, silently drop out of
scope. The untracked list catches files never added. Deleted files are excluded because there is
nothing to read; a deletion that breaks a reference shows up in PHPStan and the bug lens.

Shell state does not survive between tool calls, so paste the `BASE` SHA literally into every later
command and into the workflow args.

**A review round reviews only what changed since the review.** When the branch has an open PR that
someone other than its author has reviewed, the code before that review was already reviewed, so
the round's scope starts where the review left off. `--full` keeps the whole branch; `since <ref>`
names the start outright.

```bash
gh pr view --json number,author,url 2>/dev/null
gh api --paginate repos/{owner}/{repo}/pulls/<n>/reviews --jq '.[]' | jq -rs '
  [.[] | select(.user.login != "<author>" and .user.type != "Bot" and .state != "PENDING")
       | select(.state != "COMMENTED" or (.body // "") != "")]
  | last | select(. != null)
  | "\(.commit_id) \(.submitted_at) \(.submitted_at | fromdateiso8601)"'
git merge-base --is-ancestor <commit_id> HEAD && echo reviewed-commit-on-branch
git log --reverse --format='%H %at' <BASE>..HEAD   # author time, Unix seconds
```

Bot reviews are left out, and so is an empty-body `COMMENTED` review: that is how GitHub records a
reply in a thread. **An empty result means no review yet**: there is no round, and the scope base
is `BASE`. Compare times as Unix seconds, never as strings, since GitHub answers in UTC and
`git log` in the committer's offset.

- The reviewed commit is still on the branch: `SINCE` is that commit.
- A rebase rewrote it: `SINCE` is the parent of the first commit **authored** after the review's
  `submitted_at`. No such commit means `SINCE` is `HEAD`, and the round is the uncommitted work.
- A fix squashed into a commit from before the review hides from both. Say so in a
  `coverageNotes` line naming how `SINCE` was found.

Bind the round's files against `SINCE` into `<scratchpad>/finalize-files`, and keep the whole
branch's list in `<scratchpad>/finalize-branch-files` for the two stages that read past the round.
From here on, **the scope base** means `SINCE` in a round and `BASE` otherwise.

## 2 — Preflight

**Run Pint first, on exactly the bound set.** It is the only step that writes, and every reviewer
reads what it leaves behind, so it has to finish before the workflow starts.

```bash
grep '\.php$' <scratchpad>/finalize-files > <scratchpad>/finalize-php
[ -s <scratchpad>/finalize-php ] && xargs vendor/bin/pint --parallel --format agent < <scratchpad>/finalize-php
```

`--dirty` is the wrong scope: it skips files the branch already committed. The guard matters, since
Pint with no paths formats the whole repository. Feed the paths through `xargs`, not an unquoted
`$VAR`: the shell is zsh, which does not word-split. Record how many files Pint rewrote for the
gate table; never report its rewrites as findings.

**Measure line length, after Pint.** The command lives in `.claude/rules/coding-standards.md`
(Line Length › Checking It). Write it to `<scratchpad>/finalize-length.sh` with two changes, so the
run and the fix re-gate check the same thing:

- the scope base replaces `"$(git merge-base origin/master HEAD)"`;
- untracked PHP files join the input, since a plain `git diff` never sees them.

```bash
{
  git diff -U0 <scope base> -- '*.php' ':!*.blade.php'
  git ls-files --others --exclude-standard -- '*.php' ':!*.blade.php' |
    while read -r f; do git diff -U0 --no-index /dev/null "$f"; done
} | perl -CSD -ne '<the program from the rule, copied verbatim>'
```

```bash
bash <scratchpad>/finalize-length.sh > <scratchpad>/finalize-length; wc -l < <scratchpad>/finalize-length
```

Pass its lines as `lineLength` (an array of `file:line: …` strings, empty when clean), and the
script path as `gates.lineLengthScript`. With no PHP in the diff, pass `null`. Never fix a width
here: the workflow rolls them up per file and the fix step wraps them.

**Gather the spec.** A branch is reviewed against what its issue asked for, and a decision posted
after the issue was written can narrow or overturn it.

1. Find the issues. Take every number from all three; a spec path or issue the user named
   replaces them.

   ```bash
   git rev-parse --abbrev-ref HEAD | grep -oE '^[0-9]+'
   gh pr view --json body --jq .body 2>/dev/null | grep -oiE '(close[sd]?|fix(e[sd])?|resolve[sd]?|part of) #[0-9]+' | grep -oE '[0-9]+'
   git log --format=%B <BASE>..HEAD | grep -oE '#[0-9]+' | tr -d '#'
   ```

2. Fetch each, comments included. Drop a pull request: `gh issue view` answers for one without
   complaint, so check that the `url` holds `/issues/`.

   ```bash
   gh issue view <n> --json number,title,url,body,comments,labels > <scratchpad>/finalize-issue-<n>.json
   ```

3. Write `<scratchpad>/finalize-spec.md`. Quote, never paraphrase: the reviewer checks the code
   against these words, and a summary is where a requirement goes missing.

   ```markdown
   # #<n> <issue title>

   ## Criteria — from <source>, @<author>, <date>

   - **C1** <the criterion, verbatim>
   - **C2** …

   ## Later decisions

   > @<author>, <date>: <the comment, verbatim>

   ## Context

   <the rest of the body, verbatim>
   ```

   - **Criteria** come from the first of these that exists: the acceptance criteria of the latest
     comment holding an `## Agent Brief`, then a body section headed *Done when*, *Acceptance
     criteria*, or *Expected*. Number them `C1…` across every issue, one criterion per bullet.
   - **Later decisions** are every human comment posted after that source, with author and date.
     Bot comments are left out.

4. When no issue turns up, or an issue has no criteria, **ask** in one `AskUserQuestion`: point at
   the spec (a path or an issue), derive criteria from the issue as written, or review without a
   spec. Derived criteria are marked `(derived)`; a gap against one is a question for the user,
   never a blocker. Without a spec, pass `null`, and the workflow reports the stage as not run.

Pass `spec` as `{ file, issues, criteria }`, the last being the number of `C` entries.

**In a review round, rerun the spec stage only on news.** Rerun it against the whole branch,
passing `branchFiles`, only when an issue has a comment posted after the review's `submitted_at`.
Otherwise pass `spec: null` with `specSkipped: "no issue comment since the review of <date>"`.

**Collect the review comments** when the branch has an open PR. A round is done when every comment
is fixed or answered, and nothing else in this skill can tell which.

```bash
gh api graphql --paginate --slurp -F o=deschutesdesigngroupllc -F r=perscom-app -F n=<n> -f query='
query($o:String!,$r:String!,$n:Int!,$endCursor:String){
  repository(owner:$o,name:$r){pullRequest(number:$n){body
    reviewThreads(first:100,after:$endCursor){pageInfo{hasNextPage endCursor} nodes{
      isResolved isOutdated path line originalLine
      comments(first:100){pageInfo{hasNextPage} nodes{author{login} body createdAt url}}}}}}}' \
  > <scratchpad>/finalize-threads.json
gh api --paginate repos/{owner}/{repo}/pulls/<n>/reviews --jq '.[]' | jq -s . \
  > <scratchpad>/finalize-reviews.json
```

Write `<scratchpad>/finalize-ledger.md`, quoting every comment verbatim, numbered `R1…`:

```markdown
# PR #<n> — reviewed at <commit>, <date>

## R1 · thread · `path/to/File.php:62` · @<reviewer>, <date> · <url>

> <the comment, verbatim>

Replies: none

## R2 · review, Blocking · @<reviewer>, <date>

> <the item, verbatim>

## Current PR body

<verbatim>

## Settled in review

### `path/to/File.php:59` · @<reviewer>, <date> · resolved

> <the comment and its replies, verbatim>
```

- **Threads:** every unresolved one, outdated included, with every reply under it.
- **Settled in review:** every resolved thread, unnumbered, at the end. Without them the spec stage
  reads a change a reviewer asked for as work no criterion asked for.
- **Review bodies:** from each human reviewer's latest review with a non-empty body, one item per
  numbered point, marked `Blocking` when it sits under a heading that says so.

Pass `ledger` as `{ file, items, author }`. With no open PR, pass `null`.

**Decide the test gate.**

- `pgrep -fl 'artisan test|paratest|phpunit'` finds a run: **ask** before launching. Parallel runs
  share the MySQL `testing` and tenant test databases, so a second run drops tables under the first
  and both fail in ways that look like a broken branch. A workflow cannot ask, so this is the
  moment.
- Otherwise, collapse the test files covering the diff to the fewest common directories and write
  **one command per path**, since `artisan test` accepts exactly one path:
  `php artisan test --compact tests/Feature/Tenant/Filament/App/Widgets`. Use
  `composer test-filter <name>` when one test is the whole set. Never the full suite; that run is
  the user's.

**Discover the sources** from what exists today:

```bash
ls .claude/rules/*.md; cat .ai/rules/index.md
for f in .claude/skills/*/SKILL.md; do echo "$f: $(grep -m1 '^description:' "$f" | cut -c1-240)"; done
xargs -n1 dirname < <scratchpad>/finalize-files | sort -u | while read -r d; do
  while [ "$d" != "." ]; do [ -f "$d/CLAUDE.md" ] && echo "$d/CLAUDE.md"; d=$(dirname "$d"); done
done | sort -u
grep -E '(^|/)CLAUDE\.md$|^\.claude/rules/|^\.ai/rules/' <scratchpad>/finalize-files   # → newRules
grep -E '^##+ ' .claude/rules/coding-standards.md | sed -E 's/^#+ //'   # → standardsSections
```

`.ai/rules/index.md` maps globs to rule files: every rule file whose glob covers a changed path is
a source for that path's area. `standardsSections` turns `coding-standards.md` into a checklist:
every area agent with PHP in its slice returns a verdict per section, and any section left
unanswered is reported as unchecked coverage.

A rule or `CLAUDE.md` the branch itself changes goes in `newRules`: the branch must already obey
it, and it is the rule most likely broken elsewhere in the same diff.

**Cut the diff into areas**, at most five, each a coherent slice (one panel, one resource family,
the API, the jobs) with its tests beside it. Under about 30 files, use one area. For each, list:

- `sources`: `CLAUDE.md`, `coding-standards.md`, and `spelling.md` for any PHP; `filament.md`,
  `design-patterns.md`, `ui-text.md`, and `list-search.md` for Filament, Livewire, or Blade;
  `testing.md` for tests; `database.md` for migrations and models; `config-files.md` for `config/`;
  `one-time-operations.md` for `operations/` or permission changes; `markdown.md` for rule and
  skill files; every `.ai/rules` file whose glob matches; every `CLAUDE.md` the walk printed above
  the slice's paths
- `skills`: every sibling skill whose description matches: `docblocks` when docblocks change,
  `laravel-best-practices` for backend PHP, `tailwindcss-development` for Blade and CSS,
  `inertia-react-development` for `resources/js`, `passport-development` for OAuth,
  `cashier-stripe-development` for billing, `configuring-horizon` for queues, and whatever else the
  listing shows

Past about 200 files, five areas cannot each read everything. Say so in `coverageNotes` and let the
agents' `notReached` lists name what was missed.

**Find a simplifier**, in this order:

1. The user named one: use it.
2. `code-simplifier:code-simplifier` or `laravel-simplifier:laravel-simplifier` is in this
   session's agent types: pass it as `simplifierAgentType`.
3. The Boost MCP serves the `laravel-code-simplifier` prompt: save its text to
   `<scratchpad>/finalize-simplifier.md` and pass that path as `simplifierPromptFile`. A subagent
   cannot call an MCP prompt, so the workflow hands the file to a general-purpose agent. The
   prompt is rendered from
   `vendor/laravel/boost/src/Mcp/Prompts/LaravelCodeSimplifier/laravel-code-simplifier.blade.php`,
   a fallback only when the MCP prompt is unavailable.
4. Neither: pass both as `null`; the workflow logs the skip.

**Write the args** to `<scratchpad>/finalize-args.json`, in the shape documented at the top of
`workflows/review.js`, then **state the cost before launching**: the gates you kept, plus two
lenses, the spec stage if it runs, the ledger if there is a PR, the simplifier if present, one per
area, and one triage, at most 14. A round over a few files lands near 6. Give it in one line
("10 agents, about 10 to 15 minutes") and launch.

Models are set per stage in each script's `MODELS` table: Haiku for gates and Pint, which only
relay a command; Sonnet for rule passes and mechanical fix groups; the session model for the two
lenses, the spec stage, the ledger, triage, and any fix group carrying a behavior change.

## 3 — Review

```
Workflow({ scriptPath: "<repo root>/.claude/skills/finalize-pr/workflows/review.js", args: <the args object> })
```

Pass `args` as a JSON object, not a string. The workflow runs in the background. Start no review
of your own while it runs, since anything more would split the findings across numbering schemes.

What comes back is already normalized: duplicates merged, severity set on one scale, every S1, S2,
and D opened and checked by the triage agent (`verified: true`), and IDs `F1…Fn` assigned in final
report order. Do not re-rate, re-group, or renumber. If something looks wrong, open the file, and
if the finding falls, say so beside its ID rather than silently removing it.

## 4 — Report, then ask

Read `references/report.md` and render the report exactly as it describes: verdict line, gate
table, coverage, requirements, review comments, then findings by area. Write the tables to the
scratchpad.

Then ask with **one `AskUserQuestion` call**, so the choice and the decisions arrive together:

- **How to proceed** (single select), offering only options that apply:
  - **Fix everything**: every in-diff finding except unanswered `D`s, in one fix workflow. Say how
    many findings and agents
  - **Fix by area**: then a follow-up `multiSelect` of the areas present
  - **Blockers only**: the workflow's `blockers` list (S1 and S2). Omit when it is empty
- **One question per `D` finding** (up to three more; ask the rest in a follow-up), its `question`
  verbatim and its `options` as the choices. A `D` is never folded into a fix as an assumption.

Apply nothing until the answer arrives. A clean branch skips the question entirely.

## 5 — Fix

Build the fix args from the selection (`root`, `base`, the selected findings as returned,
`decisions` keyed by ID, and the same `gates`) and launch:

```
Workflow({ scriptPath: "<repo root>/.claude/skills/finalize-pr/workflows/fix.js", args: <fix args> })
```

The workflow groups findings so no two agents edit the same file, applies each group in dependency
order, runs Pint on what it touched, and re-runs only the gates code changes could have moved.

Leave out of the fix args, and handle as stated instead:

| Finding                                           | Handling                                                   |
|---------------------------------------------------|------------------------------------------------------------|
| Outside the diff                                  | Only if the user named it                                  |
| A `D` the user answered "leave it"                | Dropped; say so in the re-report                           |
| A change to `CLAUDE.md`, `.claude/`, `.ai/rules/` | Only if the user confirmed it                              |
| On the PR body (`file` is `PR body`)              | Drafted into the replies file; the body is never edited    |
| With no place in the code (`file` is `review`)    | Into the replies file                                      |
| `external` (`src` includes `pr`)                  | Into the replies file, naming what is needed and from whom |

Re-report against the original IDs per `references/report.md`: the selected findings with their
outcomes, then the new gate table. A `skipped` outcome carries its reason; a gate that comes back
`fail` is the next thing to ask about, not something to fix unasked.

**Draft the replies** when the run had a ledger. Read `references/replies.md` and write
`<scratchpad>/finalize-replies.md`: one reply per `R` item, in order, drafted from its final state.
Point the user at the file. Posting stays theirs.

## Boundaries

- No commit, push, `gh pr create`, `gh pr edit`, or `--comment`.
- Nothing is applied before the user picks.
- A finding outside the diff is context, not this branch's work.
- When this file and a rule disagree, the rule wins and this file has a bug.
- Deliberately repo-local: it names sibling skills and repo paths throughout.
