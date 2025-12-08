<?php

declare(strict_types=1);

namespace App\Filament\App\Pages\Forms;

use App\Models\Form;
use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Actions\Action;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;

class Forms extends Page implements HasForms, HasTable
{
    use HasPageShield;
    use InteractsWithForms;
    use InteractsWithTable;

    protected static ?string $slug = 'forms/list';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Forms';

    protected string $view = 'filament.app.pages.forms.list';

    protected static ?string $title = 'Forms';

    protected ?string $subheading = 'Access and submit critical data to your organization.';

    public function table(Table $table): Table
    {
        return $table
            ->query(Form::query())
            ->recordAction('open')
            ->columns([
                TextColumn::make('name'),
            ])
            ->recordUrl(fn (?Form $record): string => Submit::getUrl([
                'record' => $record,
            ]))
            ->groups([
                Group::make('categoryPivot.category_id')
                    ->collapsible()
                    ->label('Category')
                    ->getTitleFromRecordUsing(fn (Form $record) => $record->categoryPivot?->category?->name),
            ])
            ->groupingSettingsHidden()
            ->recordActions([
                Action::make('open')
                    ->icon('heroicon-o-pencil')
                    ->url(fn (?Form $record): string => Submit::getUrl([
                        'record' => $record,
                    ])),
            ])
            ->defaultGroup('categoryPivot.category_id');
    }
}
