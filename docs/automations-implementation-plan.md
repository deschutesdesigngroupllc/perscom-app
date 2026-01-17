# Automations Feature Implementation Plan

## Overview
Build an Automations feature that executes actions when defined triggers (model events) are caught. Each automation has one trigger, one action (webhook OR message), and optional conditions evaluated via Symfony Expression Language.

## User Requirements Confirmed
- **Tenant-scoped**: Each tenant configures their own automations
- **Triggers**: Laravel events with `AutomationTriggerable` contract, single listener handles all
- **Actions**: Either send webhook OR create a Message for notification delivery
  - **Webhook**: Send HTTP request using existing `Webhook` model
  - **Message**: Create `Message` model with dynamic recipients from expression context
- **Single action per automation**: Each automation has ONE action type; users create multiple automations for multiple actions
- **Conditions**: Optional, using Symfony Expression Language
- **Logging**: Full execution logging with context, results, and errors
- **Context Data**: Model + Changes + Causer (authenticated user)

---

## Implementation Steps

### Phase 1: Foundation (Dependencies & Database)

1. **Install Symfony Expression Language**
   ```bash
   composer require symfony/expression-language
   ```

2. **Create migrations** (tenant migrations in `database/migrations/tenant/`)
   - `create_automations_table.php`
     - `id`, `name`, `description`, `trigger` (string), `condition` (text, nullable)
     - `action_type` (string) - 'webhook' or 'message'
     - `webhook_id` (foreign key, **nullable**) - used when action_type = webhook
     - `message_template` (text, **nullable**) - message content with placeholders, used when action_type = message
     - `message_channels` (json, **nullable**) - notification channels array, used when action_type = message
     - `message_recipients_expression` (text, **nullable**) - expression to derive recipients from context
     - `enabled` (boolean), `priority` (integer), timestamps
   - `create_automations_logs_table.php`
     - `id`, `automation_id`, `trigger`, `subject_type`, `subject_id`, `causer_type`, `causer_id`, `status`, `condition_expression`, `condition_result`, `context` (json), `action_payload` (json - renamed from webhook_payload), `error_message`, `execution_time_ms`, timestamps

### Phase 2: Enums & Contract

3. **Create `AutomationTrigger` enum**
   - Path: `app/Models/Enums/AutomationTrigger.php`
   - Values mirror `WebhookEvent`: user.created, user.updated, user.deleted, assignmentrecord.created, etc.
   - Implement `HasLabel` for Filament integration
   - Add `groupedOptions()` for organized dropdown

4. **Create `AutomationActionType` enum**
   - Path: `app/Models/Enums/AutomationActionType.php`
   - Values: `webhook`, `message`
   - Implement `HasLabel` for Filament integration
   - Webhook: Sends HTTP request to configured webhook
   - Message: Creates a new Message model for notification delivery

5. **Create `AutomationLogStatus` enum**
   - Path: `app/Models/Enums/AutomationLogStatus.php`
   - Values: `pending`, `condition_failed`, `executed`, `failed`
   - Implement `HasLabel`, `HasColor` for Filament badges

6. **Create `AutomationTriggerable` contract**
   - Path: `app/Contracts/AutomationTriggerable.php`
   - Methods:
     - `getTriggerType(): string`
     - `getSubject(): Model`
     - `getChangedAttributes(): ?array`
     - `getExpressionContext(): array`
     - `getCauser(): ?Model`

### Phase 3: Models

6. **Create `Automation` model**
   - Path: `app/Models/Automation.php`
   - Relationships: `belongsTo(Webhook)` (nullable), `hasMany(AutomationLog)`
   - Casts:
     - `trigger` as `AutomationTrigger`
     - `action_type` as `AutomationActionType`
     - `message_channels` as `AsEnumCollection::of(NotificationChannel::class)`
     - `enabled` as boolean
   - Scopes: `enabled()`, `forTrigger()`, `webhookActions()`, `messageActions()`
   - Methods: `isWebhookAction(): bool`, `isMessageAction(): bool`

7. **Create `AutomationLog` model**
   - Path: `app/Models/AutomationLog.php`
   - Relationships: `belongsTo(Automation)`, `morphTo(subject)`, `morphTo(causer)`
   - Casts: `status` as `AutomationLogStatus`, `context`/`action_payload` as array

### Phase 4: Events

8. **Create abstract event base class**
   - Path: `app/Events/Automations/AbstractAutomationEvent.php`
   - Implements `AutomationTriggerable`
   - Handles: subject model, causer capture, changed attributes tracking, expression context building

9. **Create concrete event classes**
   - Path: `app/Events/Automations/`
   - User events: `UserCreated.php`, `UserUpdated.php`, `UserDeleted.php`
   - Generic record events: `RecordCreated.php`, `RecordUpdated.php`, `RecordDeleted.php`
     - Accept `AutomationTrigger` in constructor for flexibility

### Phase 5: DTOs

10. **Create DTOs in `app/Data/Automations/`**
    - `AutomationExecutionData.php` - Input data for automation execution
    - `AutomationResultData.php` - Output data with status, log, errors
    - `AutomationContextData.php` - Structured context for expressions

### Phase 6: Services

11. **Create `ExpressionLanguageService`**
    - Path: `app/Services/ExpressionLanguageService.php`
    - Singleton bound in `AppServiceProvider`
    - Methods: `evaluate()`, `validate()`, `getValidationError()`
    - Custom functions: `in_array()`, `contains()`, `starts_with()`, `ends_with()`, `changed()`, `old_value()`, `new_value()`, `empty()`, `filled()`

12. **Create `AutomationService`**
    - Path: `app/Services/AutomationService.php`
    - Methods:
      - `process(AutomationTriggerable $event)` - Find and execute matching automations
      - `executeAutomation()` - Run single automation (dispatches to appropriate action handler)
      - `executeWebhookAction()` - Send webhook using `WebhookService::dispatch()`
      - `executeMessageAction()` - Create Message model with evaluated template and recipients
      - `evaluateMessageTemplate()` - Replace placeholders in message template with context values
      - `evaluateRecipients()` - Evaluate recipients expression to get user IDs/emails
      - `testCondition()` - Test expression without executing

13. **Create `ExpressionEvaluationException`**
    - Path: `app/Exceptions/ExpressionEvaluationException.php`

### Phase 7: Listener

14. **Create `ProcessAutomationTrigger` listener**
    - Path: `app/Listeners/ProcessAutomationTrigger.php`
    - Implements `ShouldQueue`
    - Queue: `automations` (can be `default` initially)
    - Handles `AutomationTriggerable` events
    - Register in `AppServiceProvider::boot()` using `Event::listen()`

### Phase 8: Observer Integration

15. **Create `DispatchesAutomationEvents` trait**
    - Path: `app/Traits/DispatchesAutomationEvents.php`
    - Methods: `dispatchAutomationCreated()`, `dispatchAutomationUpdated()`, `dispatchAutomationDeleted()`

16. **Update existing observers** to dispatch automation events
    - Add automation event dispatch after existing webhook dispatch
    - Files to modify:
      - `app/Observers/UserObserver.php`
      - `app/Observers/AssignmentRecordObserver.php`
      - `app/Observers/AwardRecordObserver.php`
      - `app/Observers/CombatRecordObserver.php`
      - `app/Observers/ServiceRecordObserver.php`
      - `app/Observers/RankRecordObserver.php`
      - `app/Observers/QualificationRecordObserver.php`
      - `app/Observers/CalendarObserver.php`
      - `app/Observers/EventObserver.php`
      - `app/Observers/MessageObserver.php`
      - `app/Observers/SubmissionObserver.php`

### Phase 9: Filament Resources

17. **Create `AutomationResource`**
    - Path: `app/Filament/App/Resources/AutomationResource.php`
    - Navigation: Integrations group, after Webhooks
    - Form tabs:
      - **Automation**: name, description, trigger, priority, enabled
      - **Action**: action_type select with conditional fields:
        - When `webhook`: webhook select
        - When `message`: message_template textarea, message_channels checkboxes, message_recipients_expression textarea
      - **Condition**: expression textarea with syntax help
    - Table: name, trigger badge, action_type badge, enabled toggle, priority, executions count
    - Filters: trigger, action_type, enabled
    - Pages: List, Create, Edit, View

18. **Create resource pages**
    - `AutomationResource/Pages/ListAutomations.php`
    - `AutomationResource/Pages/CreateAutomation.php`
    - `AutomationResource/Pages/EditAutomation.php`
    - `AutomationResource/Pages/ViewAutomation.php`

19. **Create `LogsRelationManager`**
    - Path: `AutomationResource/RelationManagers/LogsRelationManager.php`
    - Shows automation execution history

20. **Create `AutomationLogResource`**
    - Path: `app/Filament/App/Resources/AutomationLogResource.php`
    - Read-only resource for viewing all automation logs
    - Link from ListAutomations page
    - Infolist with execution details, context, payload, errors

21. **Create log resource pages**
    - `AutomationLogResource/Pages/ListAutomationLogs.php`
    - `AutomationLogResource/Pages/ViewAutomationLog.php`

### Phase 10: Service Provider Registration

22. **Update `AppServiceProvider`**
    - Register `ExpressionLanguageService` as singleton
    - Register event listener: `Event::listen(AutomationTriggerable::class, ProcessAutomationTrigger::class)`

### Phase 11: Policies & Authorization

23. **Create `AutomationPolicy`**
    - Path: `app/Policies/AutomationPolicy.php`
    - Methods: viewAny, view, create, update, delete, restore, forceDelete, forceDeleteAny, restoreAny, replicate, reorder
    - Permission pattern: `view_automation`, `create_automation`, `update_automation`, `delete_automation`, etc.
    - Uses `HandlesAuthorization` trait

24. **Create `AutomationLogPolicy`**
    - Path: `app/Policies/AutomationLogPolicy.php`
    - Read-only permissions: viewAny, view only
    - No create/update/delete (logs are system-generated)

25. **Register policies in `AuthServiceProvider`**
    - Map `Automation::class => AutomationPolicy::class`
    - Map `AutomationLog::class => AutomationLogPolicy::class`

26. **Seed permissions**
    - Run `composer shield:seeder` after resources are created
    - Permissions auto-generated by Filament Shield

### Phase 12: Factories

27. **Create `AutomationFactory`**
    - Path: `database/factories/AutomationFactory.php`
    - Fields: name, description, trigger (random enum), webhook_id, enabled, priority, condition (nullable)

28. **Create `AutomationLogFactory`**
    - Path: `database/factories/AutomationLogFactory.php`
    - Fields: automation_id, trigger, subject_type/id, causer_type/id, status, context, webhook_payload, execution_time_ms

### Phase 13: Tests

29. **Create `ExpressionLanguageServiceTest`**
    - Path: `tests/Feature/Tenant/Services/ExpressionLanguageServiceTest.php`
    - Test: basic expressions, custom functions, validation, error handling

30. **Create `AutomationServiceTest`**
    - Path: `tests/Feature/Tenant/Services/AutomationServiceTest.php`
    - Test: process method, condition evaluation, webhook dispatch, logging

31. **Create `ProcessAutomationTriggerTest`**
    - Path: `tests/Feature/Tenant/Listeners/ProcessAutomationTriggerTest.php`
    - Test: listener queuing, event handling, error scenarios

32. **Update existing observer tests**
    - Add automation event dispatch assertions to each observer test
    - Use `Event::fake()` to verify automation events fire

---

## File Summary

### New Files (39 total)

| Path | Description |
|------|-------------|
| `database/migrations/tenant/XXXX_create_automations_table.php` | Automations table |
| `database/migrations/tenant/XXXX_create_automation_logs_table.php` | Logs table |
| `app/Models/Enums/AutomationTrigger.php` | Trigger types enum |
| `app/Models/Enums/AutomationActionType.php` | Action types enum (webhook, message) |
| `app/Models/Enums/AutomationLogStatus.php` | Log status enum |
| `app/Contracts/AutomationTriggerable.php` | Event contract |
| `app/Models/Automation.php` | Automation model |
| `app/Models/AutomationLog.php` | Log model |
| `app/Events/Automations/AbstractAutomationEvent.php` | Base event class |
| `app/Events/Automations/UserCreated.php` | User created event |
| `app/Events/Automations/UserUpdated.php` | User updated event |
| `app/Events/Automations/UserDeleted.php` | User deleted event |
| `app/Events/Automations/RecordCreated.php` | Generic record created |
| `app/Events/Automations/RecordUpdated.php` | Generic record updated |
| `app/Events/Automations/RecordDeleted.php` | Generic record deleted |
| `app/Data/Automations/AutomationExecutionData.php` | Execution DTO |
| `app/Data/Automations/AutomationResultData.php` | Result DTO |
| `app/Data/Automations/AutomationContextData.php` | Context DTO |
| `app/Services/ExpressionLanguageService.php` | Expression evaluator |
| `app/Services/AutomationService.php` | Core automation service |
| `app/Exceptions/ExpressionEvaluationException.php` | Custom exception |
| `app/Listeners/ProcessAutomationTrigger.php` | Queue listener |
| `app/Traits/DispatchesAutomationEvents.php` | Observer helper trait |
| `app/Policies/AutomationPolicy.php` | Automation authorization |
| `app/Policies/AutomationLogPolicy.php` | Log authorization (read-only) |
| `database/factories/AutomationFactory.php` | Automation test factory |
| `database/factories/AutomationLogFactory.php` | Log test factory |
| `tests/Feature/Tenant/Services/ExpressionLanguageServiceTest.php` | Expression service tests |
| `tests/Feature/Tenant/Services/AutomationServiceTest.php` | Automation service tests |
| `tests/Feature/Tenant/Listeners/ProcessAutomationTriggerTest.php` | Listener tests |
| `app/Filament/App/Resources/AutomationResource.php` | Main resource |
| `app/Filament/App/Resources/AutomationResource/Pages/ListAutomations.php` | List page |
| `app/Filament/App/Resources/AutomationResource/Pages/CreateAutomation.php` | Create page |
| `app/Filament/App/Resources/AutomationResource/Pages/EditAutomation.php` | Edit page |
| `app/Filament/App/Resources/AutomationResource/Pages/ViewAutomation.php` | View page |
| `app/Filament/App/Resources/AutomationResource/RelationManagers/LogsRelationManager.php` | Logs relation |
| `app/Filament/App/Resources/AutomationLogResource.php` | Log resource |
| `app/Filament/App/Resources/AutomationLogResource/Pages/ListAutomationLogs.php` | Log list |
| `app/Filament/App/Resources/AutomationLogResource/Pages/ViewAutomationLog.php` | Log view |

### Files to Modify (14 total)

| Path | Change |
|------|--------|
| `composer.json` | Add `symfony/expression-language` |
| `app/Providers/AppServiceProvider.php` | Register services and event listener |
| `app/Providers/AuthServiceProvider.php` | Register automation policies |
| `app/Observers/UserObserver.php` | Add automation event dispatch |
| `app/Observers/AssignmentRecordObserver.php` | Add automation event dispatch |
| `app/Observers/AwardRecordObserver.php` | Add automation event dispatch |
| `app/Observers/CombatRecordObserver.php` | Add automation event dispatch |
| `app/Observers/ServiceRecordObserver.php` | Add automation event dispatch |
| `app/Observers/RankRecordObserver.php` | Add automation event dispatch |
| `app/Observers/QualificationRecordObserver.php` | Add automation event dispatch |
| `app/Observers/CalendarObserver.php` | Add automation event dispatch |
| `app/Observers/EventObserver.php` | Add automation event dispatch |
| `app/Observers/MessageObserver.php` | Add automation event dispatch |
| `app/Observers/SubmissionObserver.php` | Add automation event dispatch |

---

## Edge Cases & Safeguards

### Circular Trigger Prevention
- Automations triggered by webhook responses could cause infinite loops
- **Solution**: Add `is_automation_triggered` flag to event context; skip automation processing when true
- Alternatively: Track automation chain depth, abort if > 3 levels deep

### Rate Limiting / Throttling
- Bulk operations (e.g., importing 1000 users) could trigger thousands of automations
- **Solution**:
  - Queue all automation executions (already in plan)
  - Configure queue workers with rate limits
  - Add `throttle_seconds` column to automations table (optional, for per-automation throttling)
  - Consider batch processing: group similar triggers within time window

### Webhook Failure Handling
- Existing `WebhookService` uses Spatie Webhook Client which has built-in retry logic
- Failed webhooks are logged in activity log
- **Additional logging**: `AutomationLog` captures `error_message` for failures
- **Recommendation**: Add link from AutomationLog to activity log entry for full webhook trace

### Soft Deletes Consideration
- **Decision**: No soft deletes for automations
- Rationale: Logs preserve history; deleted automations don't need restoration
- If needed later: Add `SoftDeletes` trait and `deleted_at` column

### Disabled Automation Behavior
- Disabled automations are skipped in `AutomationService::process()`
- No log entry created for disabled automations (reduces noise)
- Consider: Optional "dry run" logging for disabled automations

---

## API Exposure

### Decision: Not in Initial Release
- Automations are admin-facing configuration
- API exposure adds complexity (validation, rate limiting, webhook security)
- **Recommendation**: Add API in future iteration if needed

### Future API Implementation (if needed)
- Create `AutomationController` extending `Orion\Http\Controllers\Controller`
- Create `AutomationRequest` for validation
- Expose read-only endpoints initially: `GET /api/v1/automations`, `GET /api/v1/automations/{id}`
- Add create/update/delete with strict permission checks

---

## Tenant Scoping Notes

- Automations and AutomationLogs are stored in tenant databases (`database/migrations/tenant/`)
- Tenant isolation is handled automatically by Stancl/Tenancy database driver
- No explicit `tenant_id` column needed (each tenant has separate database)
- **Important**: `AutomationResource` should NOT extend `BaseResource` directly if `BaseResource` has `$isScopedToTenant = false`
- Set `protected static bool $isScopedToTenant = true;` in AutomationResource

---

## Key Design Decisions

1. **Single trigger per automation** - Simpler than multi-trigger; users can create multiple automations
2. **Single action per automation** - Either webhook OR message; users create multiple automations for multiple actions
3. **Reuse existing `WebhookService`** - Leverages proven webhook dispatch infrastructure
4. **Reuse existing `Message` model** - Messages created by automations use same notification infrastructure
5. **Generic record events** - `RecordCreated/Updated/Deleted` accept trigger type, reducing event class count
6. **Expression Language functions** - Custom functions like `changed()` make conditions more intuitive
7. **Queued processing** - `ProcessAutomationTrigger` is queued for non-blocking execution
8. **Full logging** - Every execution logged with context for debugging
9. **Backwards compatible** - Existing webhook system unchanged; automations are additive

---

## Message Action Configuration

### Message Template Placeholders
Message templates support placeholders using `{{ variable }}` syntax. Available placeholders:

```
{{ model.name }}           - Model attribute
{{ model.email }}          - Model attribute
{{ causer.name }}          - User who triggered the event
{{ causer.email }}         - Causer's email
{{ changes.field.old }}    - Old value of changed field
{{ changes.field.new }}    - New value of changed field
{{ now }}                  - Current datetime
```

**Example templates:**
```
User {{ model.name }} has been approved by {{ causer.name }}.

{{ model.name }}'s rank has been updated from {{ changes.rank_id.old }} to {{ changes.rank_id.new }}.

A new award record has been created for {{ model.user.name }}.
```

### Recipients Expression
The `message_recipients_expression` field determines who receives the message. It should evaluate to:
- A single user ID: `model["id"]`
- An array of user IDs: `[model["id"], causer["id"]]`
- A related user ID: `model["user_id"]`

**Example expressions:**
```php
// Send to the model (if it's a User)
model["id"]

// Send to the user who triggered the event
causer["id"]

// Send to both model and causer
[model["id"], causer["id"]]

// Send to the related user (for record models)
model["user_id"]

// Conditional recipient
model["supervisor_id"] != null ? model["supervisor_id"] : causer["id"]
```

### Available Notification Channels
Uses existing `NotificationChannel` enum values (same as Message model):
- `email` - Send via email
- `database` - In-app notification
- `discord` - Discord webhook
- `twilio` - SMS via Twilio

---

## Expression Language Examples

```php
// Check if user was approved
model["approved"] == true

// Check if a specific field changed
changed("status_id")

// Check old and new values
changed("rank_id") and new_value("rank_id") > old_value("rank_id")

// Check if causer exists
causer != null and causer["id"] == 1

// String checks
starts_with(model["email"], "admin")

// Null checks
filled(model["discord_user_id"])
```

---

## Database Schema Details

### automations table

```sql
CREATE TABLE automations (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT NULL,
    trigger VARCHAR(255) NOT NULL,
    condition TEXT NULL,
    action_type VARCHAR(255) NOT NULL DEFAULT 'webhook',
    webhook_id BIGINT UNSIGNED NULL,
    message_template TEXT NULL,
    message_channels JSON NULL,
    message_recipients_expression TEXT NULL,
    enabled BOOLEAN DEFAULT TRUE,
    priority INT UNSIGNED DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,

    INDEX idx_trigger (trigger),
    INDEX idx_enabled (enabled),
    INDEX idx_action_type (action_type),
    INDEX idx_trigger_enabled (trigger, enabled),
    FOREIGN KEY (webhook_id) REFERENCES webhooks(id) ON DELETE SET NULL
);
```

### automation_logs table

```sql
CREATE TABLE automation_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    automation_id BIGINT UNSIGNED NOT NULL,
    trigger VARCHAR(255) NOT NULL,
    subject_type VARCHAR(255) NULL,
    subject_id BIGINT UNSIGNED NULL,
    causer_type VARCHAR(255) NULL,
    causer_id BIGINT UNSIGNED NULL,
    status VARCHAR(255) NOT NULL,
    condition_expression TEXT NULL,
    condition_result BOOLEAN NULL,
    context JSON NULL,
    action_payload JSON NULL,
    error_message TEXT NULL,
    execution_time_ms INT UNSIGNED NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,

    INDEX idx_automation_id (automation_id),
    INDEX idx_status (status),
    INDEX idx_subject (subject_type, subject_id),
    INDEX idx_created_at (created_at),
    FOREIGN KEY (automation_id) REFERENCES automations(id) ON DELETE CASCADE
);
```

---

## Service Class Signatures

### ExpressionLanguageService

```php
class ExpressionLanguageService
{
    public function __construct();

    /**
     * Evaluate an expression with the given context
     * @throws ExpressionEvaluationException
     */
    public function evaluate(string $expression, array $context = []): mixed;

    /**
     * Validate an expression without evaluating it
     */
    public function validate(string $expression, array $allowedVariables = []): bool;

    /**
     * Get validation error message for an expression
     */
    public function getValidationError(string $expression, array $allowedVariables = []): ?string;

    /**
     * Get list of available functions for documentation
     */
    public static function getAvailableFunctions(): array;

    /**
     * Get list of available context variables
     */
    public static function getAvailableContextVariables(): array;
}
```

### AutomationService

```php
class AutomationService
{
    public function __construct(
        ExpressionLanguageService $expressionLanguageService,
        WebhookService $webhookService
    );

    /**
     * Process an automation-triggerable event
     * @return Collection<int, AutomationResultData>
     */
    public function process(AutomationTriggerable $event): Collection;

    /**
     * Execute a single automation (routes to appropriate action handler)
     */
    public function executeAutomation(Automation $automation, AutomationTriggerable $event): AutomationResultData;

    /**
     * Execute webhook action
     */
    protected function executeWebhookAction(Automation $automation, AutomationTriggerable $event): void;

    /**
     * Execute message action - creates Message model
     */
    protected function executeMessageAction(Automation $automation, AutomationTriggerable $event): void;

    /**
     * Evaluate message template, replacing placeholders with context values
     */
    protected function evaluateMessageTemplate(string $template, array $context): string;

    /**
     * Evaluate recipients expression to get array of user IDs
     */
    protected function evaluateRecipients(string $expression, array $context): array;

    /**
     * Test an automation condition without executing the action
     */
    public function testCondition(string $condition, array $context): array;
}
```

---

## AutomationTriggerable Contract

```php
interface AutomationTriggerable
{
    /**
     * Get the trigger type identifier (e.g., 'user.created', 'awardrecord.updated')
     */
    public function getTriggerType(): string;

    /**
     * Get the subject model that triggered the event
     */
    public function getSubject(): Model;

    /**
     * Get the attributes that were changed (for update events)
     * Returns null for create/delete events
     *
     * @return array<string, array{old: mixed, new: mixed}>|null
     */
    public function getChangedAttributes(): ?array;

    /**
     * Get context data available for expression evaluation
     *
     * @return array<string, mixed>
     */
    public function getExpressionContext(): array;

    /**
     * Get the user who caused this event (if applicable)
     */
    public function getCauser(): ?Model;
}
```

---

## Expression Context Variables

| Variable | Type | Description |
|----------|------|-------------|
| `model` | array | The subject model data as an array |
| `model_type` | string | The fully qualified class name of the model |
| `model_id` | int/string | The primary key of the model |
| `changes` | array/null | Array of changed attributes with old and new values |
| `causer` | array/null | The user who triggered the event (if available) |
| `causer_id` | int/string/null | The ID of the user who triggered the event |
| `now` | Carbon | The current datetime |

---

## Custom Expression Functions

| Function | Description | Example |
|----------|-------------|---------|
| `in_array(needle, haystack)` | Check if value exists in array | `in_array(model["status_id"], [1, 2, 3])` |
| `contains(haystack, needle)` | Check if string contains substring | `contains(model["email"], "@example.com")` |
| `starts_with(haystack, needle)` | Check if string starts with | `starts_with(model["name"], "Admin")` |
| `ends_with(haystack, needle)` | Check if string ends with | `ends_with(model["email"], ".gov")` |
| `changed(key)` | Check if attribute was changed | `changed("status_id")` |
| `old_value(key)` | Get old value of changed attribute | `old_value("rank_id")` |
| `new_value(key)` | Get new value of changed attribute | `new_value("rank_id")` |
| `empty(value)` | Check if value is empty | `empty(model["notes"])` |
| `filled(value)` | Check if value is not empty | `filled(model["discord_user_id"])` |
