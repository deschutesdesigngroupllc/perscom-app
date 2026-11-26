# AI Agents and Tools

This directory holds the laravel/ai agents and the tools they call. `Agents/CommandCopilot` answers
questions about personnel and proposes administrative changes; `Agents/OrganizationBlueprintAgent`
returns a structured organization blueprint. `Tools/` holds the copilot's read tools, and
`Tools/Proposals/` holds its propose tools.

## The Copilot Flow

1. The user asks in the `App\Livewire\Filament\App\Copilot` slide-over. It calls
   `App\Services\Ai\CopilotService::ask()`, which checks `AiUsageService::ensureWithinLimit()`,
   creates an empty `ProposedChangeSetData`, and prompts a `CommandCopilot` built with the acting
   user and that change set (at most 12 steps).
2. The agent reads with `SearchMembers`, `GetMember`, and `ListOrganization`, each constructed with
   the actor and gated on the actor's policies (`viewAny` or `view`). A denied read returns a
   message to the model rather than throwing.
3. The agent proposes with the `Propose*` tools. Each appends one `ProposedOperationData` to the
   shared change set and writes nothing.
4. `ask()` returns the answer text and the change set. The Livewire component stores the change
   set as an array (`$changeSet`) so it survives requests, and shows each operation's `summary` in
   the review modal.
5. "Apply Changes" rebuilds the change set and hands it to `App\Actions\Copilot\ApplyChangeSet`,
   which validates every operation, authorizes every operation for the actor, and only then creates
   the records in one transaction, logging the activity under the `copilot` source.

## Proposal Tools Never Write

`ProposalTool` is the base for every propose tool. Its contract:

- `handle()` validates the input, calls the subclass's `propose()`, adds the result to the change
  set, and returns a string telling the model nothing has changed yet. It must never touch the
  database beyond reads used to build the summary. Writing happens only in `ApplyChangeSet`, after
  the user confirms.
- Proposing is not authorized. A member who cannot create rank records can still get one proposed;
  `ApplyChangeSet::authorize()` rejects it at apply time with an `AuthorizationException`, and
  nothing in the set is applied. Do not move authorization into the tools on the assumption that
  apply will skip it: the change set round-trips through a public Livewire property, so the client
  can alter it, and apply must re-check everything regardless.
- Validation failures are returned to the model as a "The proposal was not added" string, not
  thrown, so the agent can correct its input and retry within its step budget.
- Every proposal targets members through `user_ids`: required, distinct, existing user ids, at
  most `ProposalTool::MAX_MEMBERS` (200). Subclasses spread `memberSchema()` into their `schema()`
  and add only their own fields in `rules()`; the base merges the `user_ids` rules in.
- `summary` is what the user reviews and what the activity log records, so it must describe the
  change in plain words. `describeMembers()` names the first three members alphabetically and
  counts the rest.

`ProposedOperationData` carries a `CopilotOperationType`, the `userIds`, a `targetId` (the rank,
unit, award, qualification, or status; unused for messages), optional `text` (the record text, or
the message body), and type-specific `attributes`.

| Tool                          | Operation type         | `targetId`    | `attributes`                          |
|-------------------------------|------------------------|---------------|---------------------------------------|
| `ProposeRankRecords`          | `RANK_RECORD`          | rank          | `type`                                |
| `ProposeAssignmentRecords`    | `ASSIGNMENT_RECORD`    | unit          | `type`, `position_id`, `specialty_id` |
| `ProposeAwardRecords`         | `AWARD_RECORD`         | award         | none                                  |
| `ProposeQualificationRecords` | `QUALIFICATION_RECORD` | qualification | none                                  |
| `ProposeStatusChanges`        | `STATUS_RECORD`        | status        | none                                  |
| `ProposeMessage`              | `MESSAGE`              | none          | `channels`                            |

`ApplyChangeSet` also reads `attributes['unit_slot_id']` for assignments. No copilot tool sets it;
`App\Filament\Actions\AssignUsersBulkAction` does.

## Authorization at Apply Time

`CopilotOperationType::getAuthorizableModel()` and `getAuthorizationAbility()` decide the check:
`create` on the record model for most types, `create` on `Message` for messages, and `update` on
each affected `User` for status changes. The same `ApplyChangeSet` path backs the Filament member
and bulk actions through `App\Filament\Actions\Concerns\AppliesChangeSets`, so the copilot and the
manual actions share one set of rules, observers, and notifications.

## Prompt Injection

Tool output includes member-entered names, notes, and record text. `CommandCopilot::instructions()`
tells the model to treat it strictly as data, never to claim a change was made, and never to guess
an id. Keep those rules when editing the prompt, and keep tool return strings factual so the model
does not report a proposal as an applied change.

## Common Changes

**Add a new proposal type.**

1. Add a case to `App\Models\Enums\CopilotOperationType` with its label, color, authorizable model,
   and ability.
2. Create a `ProposalTool` subclass in `Tools/Proposals/` with `description()`, `schema()`,
   `rules()`, and `propose()`.
3. Register it in `CommandCopilot::tools()`, constructed with `$this->changeSet`.
4. Handle the type in `ApplyChangeSet::validate()` (target model) and `applyToUser()` or `apply()`.
   Both `match` expressions are exhaustive, so a missing case throws.
5. Cover it in `tests/Feature/Tenant/Ai/Tools/Proposals/ProposalToolTest.php` (queues an operation
   and writes nothing) and `tests/Feature/Tenant/Actions/Copilot/ApplyChangeSetTest.php`.

**Add a read tool.** Construct it with the actor, gate it with the actor's policies, and register
it in `CommandCopilot::tools()`. Member payloads go through `Tools\Concerns\DescribesMembers` so
every tool describes a member the same way. Tests live in `tests/Feature/Tenant/Ai/Tools/`.

## Organization Blueprints

`OrganizationBlueprintAgent` has no tools and returns structured output only. It is driven by
`App\Services\Ai\OrganizationBlueprintService` (`generate()`, `refine()`, and `extend()`, each
behind `AiUsageService::ensureWithinLimit()`), which validates the response into
`OrganizationBlueprintData`. The agent never creates anything; the service's callers apply the
blueprint.
