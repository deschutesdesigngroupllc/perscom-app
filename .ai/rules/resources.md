---
paths:
  - 'app/Filament/**/Resources/**'
---

# Resources

## New resources use the split Filament structure
Create new resources in Filament's split layout: Resources/{Plural}/{Model}Resource.php with Schemas/{Model}Form.php, Schemas/{Model}Infolist.php, Tables/{Plural}Table.php, and Pages/. Do not define form()/table()/infolist() inline on new resources. Existing inline resources may stay as-is until migrated.

## List pages declare a subheading
Give every ListRecords page a one-sentence protected ?string $subheading describing what the resource is for.

## Tabbed resource forms persist the active tab
Wrap resource forms in Tabs::make()->columnSpanFull()->persistTabInQueryString() and give each Tab an ->icon(Heroicon::...).

## Form fields carry helper text
Add a short ->helperText() sentence to each resource form field, phrased like 'The name of the rank.'

## Tables define an empty state and export action
Resource tables set ->emptyStateIcon(Heroicon::Outlined...) and ->emptyStateDescription('There are no {plural} to view. Create one to get started.'). Add ExportBulkAction::make()->exporter(XExporter::class)->icon(Heroicon::OutlinedDocumentArrowDown) before the BulkActionGroup in toolbarActions; exporters live in App\Filament\Exports.
