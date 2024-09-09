<?php

declare(strict_types=1);

namespace App\Filament\App\Pages\Forms;

use App\Models\Form;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class Forms extends Page implements HasForms, HasTable
{
    use HasPageShield;
    use InteractsWithForms;
    use InteractsWithTable;

    protected static ?string $slug = 'forms/list';

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Forms';

    protected static string $view = 'filament.app.pages.forms.list';

    protected static ?string $title = 'Forms';

    public function table(Table $table): Table
    {
        return $table
            ->query(Form::query())
            ->recordAction('open')
            ->columns([
                TextColumn::make('name'),
            ])
            ->recordUrl(fn (?Form $record) => Submit::getUrl([
                'record' => $record,
            ]))
            ->actions([
                Action::make('open')
                    ->icon('heroicon-o-pencil')
                    ->url(fn (?Form $record) => Submit::getUrl([
                        'record' => $record,
                    ])),
            ]);
    }
}
