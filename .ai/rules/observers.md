---
paths:
  - 'app/Observers/**'
---

# Observers

## Observer lifecycle order
In observer lifecycle methods, send notifications first, then fan out to subscribed webhooks via WebhookService::dispatch(), then raise automation events through the DispatchesAutomationEvents helpers (dispatchAutomationCreated/Updated/Deleted). Do not construct automation events directly.
