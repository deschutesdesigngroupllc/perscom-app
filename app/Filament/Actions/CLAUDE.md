# Member Actions

This directory holds the shared Filament actions that act on members: the "Add Record" menu and its
single-member and bulk record actions, the archive and delete actions for a member, and the
`AppliesChangeSets` concern they share. `app/Filament/Forms/Components/MemberSelect.php`, the
member picker the record forms use, is covered here too because the menu hands members to it.

## Where the Actions Appear

- **Members table** (`UserResource`): each row has `MemberRecordActions::group()` plus
  `ArchiveMemberAction` and `DeleteMemberAction` under "More"; the bulk menu has
  `MemberRecordActions::bulk()` and the stock `DeleteBulkAction`.
- **Profile** (`UserResource/Pages/ViewUser.php`): the header has the "Add Record" group plus
  `ArchiveMemberAction` and `DeleteMemberAction` under "More".
- **Edit page** (`UserResource/Pages/EditUser.php`): the header has `DeleteMemberAction`.
- **Profile relation managers**: the Rank, Award, Assignment, and Qualification record managers
  mount `PromoteMemberAction`, `AwardMemberAction`, `AssignMemberAction`, and
  `QualifyMemberAction` as header actions.

## The Add Record Menu

`MemberRecordActions` is a static builder, not an action. `make()` returns one entry per record type
for a single member, `bulk()` returns the same types for rows selected in the members table, and
`group()` wraps `make()` in the "Add Record" `ActionGroup`.

Record types fall into two shapes:

- **Dialog types** (rank, award, assignment, qualification) open a short modal and write records
  directly. Each has a single-member action here (`PromoteMemberAction` and its siblings) and a bulk
  action (`PromoteUsersBulkAction` and its siblings).
- **Full-form types** (training, combat, service) have forms too rich for a modal, so the action
  is a link to the resource's create page with the member ids in the `user_id` query string. The
  single-member link uses `->url()`; the bulk link redirects from `->action()` with the selected ids
  sorted ascending.

The single-member dialog actions do not own their forms or their writes. Each one calls its bulk
sibling's `formSchema()` and `operationFor()` with a one-member collection, so a form or operation
change belongs in the bulk action and reaches both paths. `PromoteMemberAction` adds one thing on
top: `fillForm()` preselects `PromotionEligibilityService::nextRankFor()` of the member's current
rank, which is the rank ordered just before it (`Rank` is globally scoped to `order` ascending), and
leaves it empty for a member with no rank or the most senior rank.

### Resolving the Member

A single-member action runs in three contexts: the profile page (the record is the `User`), a row
of the members table (same), and a relation manager on the profile (the record is the related
record, or none for a header action, and the member is the owner record). `resolveMember()` returns
the `User` from whichever applies, or `null`, and the action returns without writing when it is
`null`. `PromoteMemberAction::resolveMember()` is the copy `QualifyMemberAction` and
`MemberRecordActions::linkToCreate()` call; `AssignMemberAction` and `AwardMemberAction` carry
identical copies of their own.

`ArchiveMemberAction` and `DeleteMemberAction` type their closures as `User $record` and do not
resolve an owner, so they only work where the record is the member itself.

### Names Must Stay Distinct

Tests and pages mount these by name, and the members table carries row and bulk actions in one
component: single-member actions are `promoteMember`, `awardMember`, `assignMember`,
`qualifyMember`, `recordTraining`, `recordCombat`, and `recordService`; bulk actions are
`promote`, `award`, `assign`, `qualify`, `recordTrainingBulk`, `recordCombatBulk`, and
`recordServiceBulk`. `ArchiveMemberAction` is `archiveMember`. `DeleteMemberAction` keeps the stock
`delete` name so it takes the place of Filament's delete action.

## Writes Go Through the Change Set

Every dialog action and `ArchiveMemberAction` writes through
`AppliesChangeSets::applyOperation()`, which wraps one `ProposedOperationData` in a change set and
hands it to `App\Actions\Copilot\ApplyChangeSet::handle()` as the signed-in user. That is the same
path Copilot proposals and the starting rank, assignment, and status of `CreateUser` use, so:

- Every operation is validated and authorized before anything is written, and the writes run in
  one transaction.
- Records are created through Eloquent, so their observers send the usual notifications, webhooks,
  and automations. That is what the "They are notified as usual" modal copy promises.
- One activity log entry is written per change set with the source as its log name: single-member
  actions pass `'profile action'`, and bulk actions use the default `'bulk action'`.
- An `AuthorizationException` becomes a failure notification saying nothing was applied, and a
  `ValidationException` becomes a failure notification with its first message. A success
  notification reads "Created N Record(s)".

`ArchiveMemberAction` builds a `STATUS_RECORD` operation itself. `ApplyChangeSet` attaches the
status to the member's `statuses()` history with the note as pivot `text`, then sets `status_id`
with `forceFill()->save()`, so `UserObserver::updated()` fires. The member and every record they
own are kept.

## Permissions

Filament gives a plain `Action` no policy check, so each action gates its own visibility:

| Action                              | Visible when                                   |
|-------------------------------------|------------------------------------------------|
| Promote, Award, Assign, and Qualify | `create` on the record model                   |
| Training, Combat, and Service links | `create` on the record model                   |
| `ArchiveMemberAction`               | `update` on the member and any `Status` exists |
| `DeleteMemberAction`                | the resource's delete authorization            |

The record models are `RankRecord`, `AwardRecord`, `AssignmentRecord`, `QualificationRecord`,
`TrainingRecord`, `CombatRecord`, and `ServiceRecord`. The resource's delete authorization resolves
to `UserPolicy::delete`.

Visibility is not the only check. `ApplyChangeSet` authorizes again: `create` on the operation's
model for record types, and `update` on each member for a status change. Keep a new action's
visibility gate matching what `ApplyChangeSet` will authorize, or the button shows and then fails.
`DeleteMemberAction` must keep extending `DeleteAction`: Filament resource pages and relation
managers authorize and redirect any `DeleteAction` subclass through the resource, and a plain
`Action` would lose both. The link actions only check `create`; the target create page and its
validation do the rest.

## Archive and Delete

`ArchiveMemberAction::suggestedStatusId()` preselects the first status, in `Status` order, whose
lowercased name contains one of `LEAVING_STATUS_KEYWORDS` (substring match, so `discharg` matches
"Honorably Discharged"). The admin can pick any status; nothing restricts the choice to a leaving
status.

Deleting a member removes every record they own through `cascadeOnDelete()` foreign keys on the
record tables, not through Eloquent, so per-record observers, webhooks, and automations do not fire
for those records; only `UserObserver::deleted()` runs. `DeleteMemberAction` therefore states the
count from `serviceRecordCount()`, points to archiving, and requires typing the member's name
exactly (the `in` rule is case-sensitive). The count covers the seven record relations listed in
`serviceRecordCount()` and not the status history. The members table's bulk `DeleteBulkAction` has
none of these guards.

## MemberSelect

`MemberSelect` is the member picker for record, document, event, message, and task forms. It
searches on the server instead of preloading every member: `searchMembers()` matches `name` or
`email` with `LIKE`, orders by name, and stops at `SEARCH_LIMIT` (50). Labels read "rank
abbreviation, name · unit" so members with the same name can be told apart, and `labelsFor()`
loads the ranks and units for a whole result set in two queries.

- Search matches email even where `DashboardSettings::user_hidden_fields` hides email on the
  profile. Labels never show the email.
- Chaining `->relationship()` after `make()` replaces the search with Filament's relationship
  search on the title attribute, so email no longer matches. Labels still come from
  `memberLabel()` per record, which lazy loads each member's rank and unit. The `author_id` fields
  in the record resources use this form.
- `prefillFromQuery()` sets the field's default from the query parameter matching the field name,
  accepting `?user_id=4` or `?user_id[]=4&user_id[]=9`. Non-numeric values are dropped; a
  single-value select takes the first id. Only the training, combat, and service resources call it
  on `user_id`, and those fields are `multiple()` on create, which the bulk links rely on.

## Common Changes

**A new dialog record type.** Add a `CopilotOperationType` case with its authorizable model, add
its validate and apply branches in `ApplyChangeSet`, then add a `...UsersBulkAction` with
`formSchema()`, `operationFor()`, and a short `getDefaultName()`, and a `...MemberAction` that
reuses both. Register them in `MemberRecordActions::make()` and `bulk()`, add the single-member
action as a header action on the matching profile relation manager, and cover the profile, row,
relation manager, and bulk paths in
`tests/Feature/Tenant/Filament/Actions/MemberRecordActionsTest.php`.

**A new full-form record type.** Add `linkToCreate()` and `bulkLinkToCreate()` entries, give the
resource's `user_id` `MemberSelect` `->prefillFromQuery()` and `->multiple()` on create, and add a
URL assertion to `MemberRecordActionsTest`.

**Any new record table owned by a member.** Add its relation to `serviceRecordCount()` so the
delete warning stays accurate, and give its `user_id` foreign key `cascadeOnDelete()`.

Archive and delete behavior is tested in
`tests/Feature/Tenant/Filament/Actions/ArchiveAndDeleteMemberTest.php`, bulk actions in
`UserBulkActionsTest.php` beside it, and the picker in
`tests/Feature/Tenant/Filament/Forms/Components/MemberSelectTest.php`.
