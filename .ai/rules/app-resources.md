---
paths:
  - 'app/Filament/App/Resources/**'
---

# App Resources

## App resources extend BaseResource
App-panel resources extend App\Filament\App\Resources\BaseResource (which disables Filament tenant scoping), not Filament's Resource. Tenancy comes from the subdomain/database, not Filament tenancy.

## Record pages compose the shared record traits
Create pages for personnel record resources (*RecordResource) use CanCreateBatchRecords, SetsUserFromRequestQuery, ConfiguresModelNotifications, and SavesAndHydratesCustomFieldData from App\Traits\Filament. Their Edit pages use SavesAndHydratesCustomFieldData.
