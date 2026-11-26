# Git Workflow

## Branches

Work lands on `develop`, and pull requests target `master`. A branch for an issue is named
`{issue-number}-{description}`; without an issue, `{type}/{description}` with a type of
`feature`, `fix`, `hotfix`, or `chore`.

Rebase a feature branch onto its base rather than merging the base in.

## Commits

Commit only when asked. Each commit is atomic: one logical change, with the tests that cover it.

- **Group by unit.** A class and its test, a migration and the model change it serves, a rule
  and the code that follows it go together. A refactor touching twenty files is one commit.
- **Order by dependency.** Commit what nothing else depends on first, then work up, so every
  commit builds and passes on its own.
- **Split a file that carries two changes.** Stage the earlier change alone, commit it, then
  restore the file and commit the rest.

### Messages

- A title in the imperative, describing the outcome: "Hide tab containers when all of their tabs
  are hidden". Aim for 50 characters and never pass 72.
- A body only when it explains why, wrapped at 72 columns.
- No "Generated with" or "Co-Authored-By" lines.

## Pull Requests

Write the description to be scanned:

- Lead with what the change does, then show how, one short paragraph at most before a code block.
- Separate major sections with `---`, and use code blocks and tables rather than describing a
  pattern in prose.
- End with the test plan as a checkbox list.
