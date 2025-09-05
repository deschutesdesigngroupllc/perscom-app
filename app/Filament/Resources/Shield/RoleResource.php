<?php

declare(strict_types=1);

namespace App\Filament\Resources\Shield;

use App\Filament\Resources\Shield\RoleResource\Pages\CreateRole;
use App\Filament\Resources\Shield\RoleResource\Pages\EditRole;
use App\Filament\Resources\Shield\RoleResource\Pages\ListRoles;
use App\Filament\Resources\Shield\RoleResource\Pages\ViewRole;
use App\Models\Role;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Panel;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class RoleResource extends Resource implements HasShieldPermissions
{
    protected static ?string $recordTitleAttribute = 'name';

    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'delete',
            'delete_any',
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make()
                    ->schema([
                        Section::make()
                            ->columnSpanFull()
                            ->schema([
                                TextInput::make('name')
                                    ->columnSpanFull()
                                    ->label(__('filament-shield::filament-shield.field.name'))
                                    ->unique(ignoreRecord: true)
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('guard_name')
                                    ->label(__('filament-shield::filament-shield.field.guard_name'))
                                    ->default(Utils::getFilamentAuthGuard())
                                    ->hidden()
                                    ->nullable()
                                    ->maxLength(255),
                            ])
                            ->columns([
                                'sm' => 2,
                                'lg' => 3,
                            ]),
                    ]),
                Tabs::make('Permissions')
                    ->contained()
                    ->tabs([
                        static::getTabFormComponentForResources(),
                        static::getTabFormComponentForPage(),
                        static::getTabFormComponentForWidget(),
                        static::getTabFormComponentForCustomPermissions(),
                    ])
                    ->columnSpan('full'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->badge()
                    ->label(__('filament-shield::filament-shield.column.name'))
                    ->formatStateUsing(fn ($state): string => Str::headline($state))
                    ->sortable()
                    ->colors(['primary']),
                TextColumn::make('permissions_count')
                    ->badge()
                    ->label(__('filament-shield::filament-shield.column.permissions'))
                    ->counts('permissions')
                    ->sortable()
                    ->colors(['success']),
                TextColumn::make('updated_at')
                    ->label(__('filament-shield::filament-shield.column.updated_at'))
                    ->toggleable(false)
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make()
                    ->visible(fn (Role $record): bool => static::canEdit($record)),
                DeleteAction::make()
                    ->visible(fn (Role $record): bool => static::canDelete($record)),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRoles::route('/'),
            'create' => CreateRole::route('/create'),
            'view' => ViewRole::route('/{record}'),
            'edit' => EditRole::route('/{record}/edit'),
        ];
    }

    public static function getCluster(): ?string
    {
        return Utils::getResourceCluster() ?? static::$cluster;
    }

    public static function getModel(): string
    {
        return Utils::getRoleModel();
    }

    public static function getModelLabel(): string
    {
        return __('filament-shield::filament-shield.resource.label.role');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament-shield::filament-shield.resource.label.roles');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return Utils::isResourceNavigationRegistered();
    }

    public static function getNavigationGroup(): ?string
    {
        return Utils::isResourceNavigationGroupEnabled()
            ? __('filament-shield::filament-shield.nav.group')
            : '';
    }

    public static function getNavigationLabel(): string
    {
        return __('filament-shield::filament-shield.nav.role.label');
    }

    public static function getNavigationIcon(): string
    {
        return __('filament-shield::filament-shield.nav.role.icon');
    }

    public static function getNavigationSort(): ?int
    {
        return Utils::getResourceNavigationSort();
    }

    public static function getSlug(?Panel $panel = null): string
    {
        return Utils::getResourceSlug();
    }

    public static function getNavigationBadge(): ?string
    {
        return Utils::isResourceNavigationBadgeEnabled()
            ? strval(static::getEloquentQuery()->count())
            : null;
    }

    public static function isScopedToTenant(): bool
    {
        return Utils::isScopedToTenant();
    }

    public static function canGloballySearch(): bool
    {
        return Utils::isResourceGloballySearchable() && count(static::getGloballySearchableAttributes()) && static::canViewAny();
    }

    public static function getResourceEntitiesSchema(): ?array
    {
        return collect(FilamentShield::getResources())
            ->sortKeys()
            ->map(function (array $entity): Section {
                $sectionLabel = strval(
                    static::shield()->hasLocalizedPermissionLabels()
                    ? FilamentShield::getLocalizedResourceLabel($entity['fqcn'])
                    : $entity['model']
                );

                return Section::make($sectionLabel)
                    ->description(fn (): HtmlString => new HtmlString('<span style="word-break: break-word;">'.Utils::showModelPath($entity['fqcn']).'</span>'))
                    ->compact()
                    ->schema([
                        static::getCheckBoxListComponentForResource($entity),
                    ])
                    ->columnSpan(static::shield()->getSectionColumnSpan())
                    ->collapsible();
            })
            ->toArray();
    }

    public static function getResourceTabBadgeCount(): ?int
    {
        return collect(FilamentShield::getResources())
            ->map(fn ($resource): int => count(static::getResourcePermissionOptions($resource)))
            ->sum();
    }

    public static function getResourcePermissionOptions(array $entity): array
    {
        return collect(Utils::getResourcePermissionPrefixes($entity['fqcn']))
            ->flatMap(function (string $permission) use ($entity): array {
                $name = $permission.'_'.$entity['resource'];
                $label = static::shield()->hasLocalizedPermissionLabels()
                    ? FilamentShield::getLocalizedResourcePermissionLabel($permission)
                    : $name;

                return [
                    $name => $label,
                ];
            })
            ->toArray();
    }

    public static function setPermissionStateForRecordPermissions(Component $component, string $operation, array $permissions, ?Model $record): void
    {
        if (in_array($operation, ['edit', 'view'])) {
            if (blank($record)) {
                return;
            }

            if ($component->isVisible() && $permissions !== []) {
                $component->state(
                    collect($permissions)
                        /** @phpstan-ignore-next-line */
                        ->filter(fn ($value, $key) => $record->checkPermissionTo($key))
                        ->keys()
                        ->toArray()
                );
            }
        }
    }

    public static function getPageOptions(): array
    {
        return collect(FilamentShield::getPages())
            ->flatMap(fn ($page): array => [
                $page['permission'] => static::shield()->hasLocalizedPermissionLabels()
                    ? FilamentShield::getLocalizedPageLabel($page['class'])
                    : $page['permission'],
            ])
            ->toArray();
    }

    public static function getWidgetOptions(): array
    {
        return collect(FilamentShield::getWidgets())
            ->flatMap(fn ($widget): array => [
                $widget['permission'] => static::shield()->hasLocalizedPermissionLabels()
                    ? FilamentShield::getLocalizedWidgetLabel($widget['class'])
                    : $widget['permission'],
            ])
            ->toArray();
    }

    public static function getCustomPermissionOptions(): ?array
    {
        return FilamentShield::getCustomPermissions()
            ->mapWithKeys(fn ($customPermission): array => [
                $customPermission => static::shield()->hasLocalizedPermissionLabels() ? str($customPermission)->headline()->toString() : $customPermission,
            ])
            ->toArray();
    }

    public static function getTabFormComponentForResources(): Tab
    {
        return static::shield()->hasSimpleResourcePermissionView()
            ? static::getTabFormComponentForSimpleResourcePermissionsView()
            : Tab::make('resources')
                ->label(__('filament-shield::filament-shield.resources'))
                ->visible(fn (): bool => Utils::isResourceEntityEnabled())
                ->badge(static::getResourceTabBadgeCount())
                ->schema([
                    Grid::make()
                        ->schema(static::getResourceEntitiesSchema())
                        ->columns(static::shield()->getGridColumns()),
                ]);
    }

    public static function getCheckBoxListComponentForResource(array $entity): Component
    {
        $permissionsArray = static::getResourcePermissionOptions($entity);

        return static::getCheckboxListFormComponent($entity['resource'], $permissionsArray, false);
    }

    public static function getTabFormComponentForPage(): Tab
    {
        $options = static::getPageOptions();
        $count = count($options);

        return Tab::make('pages')
            ->label(__('filament-shield::filament-shield.pages'))
            ->visible(fn (): bool => Utils::isPageEntityEnabled() && $count > 0)
            ->badge($count)
            ->schema([
                static::getCheckboxListFormComponent('pages_tab', $options),
            ]);
    }

    public static function getTabFormComponentForWidget(): Tab
    {
        $options = static::getWidgetOptions();
        $count = count($options);

        return Tab::make('widgets')
            ->label(__('filament-shield::filament-shield.widgets'))
            ->visible(fn (): bool => Utils::isWidgetEntityEnabled() && $count > 0)
            ->badge($count)
            ->schema([
                static::getCheckboxListFormComponent('widgets_tab', $options),
            ]);
    }

    public static function getTabFormComponentForCustomPermissions(): Tab
    {
        $options = static::getCustomPermissionOptions();
        $count = count($options);

        return Tab::make('custom')
            ->label(__('filament-shield::filament-shield.custom'))
            ->visible(fn (): bool => Utils::isCustomPermissionEntityEnabled() && $count > 0)
            ->badge($count)
            ->schema([
                static::getCheckboxListFormComponent('custom_permissions', $options),
            ]);
    }

    public static function getTabFormComponentForSimpleResourcePermissionsView(): Tab
    {
        $options = FilamentShield::getAllResourcePermissions();
        $count = count($options);

        return Tab::make('resources')
            ->label(__('filament-shield::filament-shield.resources'))
            ->visible(fn (): bool => Utils::isResourceEntityEnabled() && $count > 0)
            ->badge($count)
            ->schema([
                static::getCheckboxListFormComponent('resources_tab', $options),
            ]);
    }

    public static function getCheckboxListFormComponent(string $name, array $options, bool $searchable = true): Component
    {
        return CheckboxList::make($name)
            ->hiddenLabel()
            ->options(fn (): array => $options)
            ->searchable($searchable)
            ->afterStateHydrated(
                fn (Component $component, string $operation, ?Model $record) => static::setPermissionStateForRecordPermissions(
                    component: $component,
                    operation: $operation,
                    permissions: $options,
                    record: $record
                )
            )
            ->dehydrated(fn ($state): bool => ! blank($state))
            ->bulkToggleable()
            ->gridDirection('row')
            ->columns(static::shield()->getCheckboxListColumns())
            ->columnSpan(static::shield()->getCheckboxListColumnSpan());
    }

    public static function shield(): FilamentShieldPlugin
    {
        return FilamentShieldPlugin::get();
    }

    /**
     * @param  Role  $record
     */
    public static function canEdit(Model $record): bool
    {
        return $record->is_custom_role && parent::canEdit($record);
    }

    /**
     * @param  Role  $record
     */
    public static function canDelete(Model $record): bool
    {
        return $record->is_custom_role && parent::canDelete($record);
    }
}
