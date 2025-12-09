<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\Pages;

use App\Filament\App\Resources\BaseResource;
use App\Filament\App\Resources\Pages\Pages\CreatePage;
use App\Filament\App\Resources\Pages\Pages\EditPage;
use App\Filament\App\Resources\Pages\Pages\ListPages;
use App\Filament\App\Resources\Pages\Schemas\PageForm;
use App\Filament\App\Resources\Pages\Schemas\PageInfolist;
use App\Filament\App\Resources\Pages\Tables\PagesTable;
use App\Models\Page;
use App\Models\Scopes\HiddenScope;
use App\Models\Scopes\VisibleScope;
use BackedEnum;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class PageResource extends BaseResource
{
    protected static ?string $model = Page::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBookOpen;

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|UnitEnum|null $navigationGroup = 'System';

    protected static ?int $navigationSort = 11;

    public static function form(Schema $schema): Schema
    {
        return PageForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PageInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PagesTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                VisibleScope::class,
                HiddenScope::class,
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPages::route('/'),
            'create' => CreatePage::route('/create'),
            'edit' => EditPage::route('/{record}/edit'),
        ];
    }
}
