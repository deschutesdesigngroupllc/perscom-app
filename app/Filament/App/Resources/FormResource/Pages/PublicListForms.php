<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\FormResource\Pages;

use App\Filament\App\Resources\FormResource;
use App\Models\Form;
use App\Models\Submission;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Resources\Pages\Page;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Gate;

class PublicListForms extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static string $resource = FormResource::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Forms';

    protected static string $view = 'filament.app.pages.forms.list';

    protected static ?string $title = 'Forms';

    public static function authorizeResourceAccess(): void
    {
        abort_unless(Gate::check('create', Submission::class), 403);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Form::query())
            ->recordAction('open')
            ->columns([
                TextColumn::make('name'),
            ])
            ->recordUrl(fn (?Form $record) => FormResource::getUrl('submit', [
                'record' => $record,
            ]))
            ->actions([
                Action::make('open')
                    ->icon('heroicon-o-pencil')
                    ->url(fn (?Form $record) => FormResource::getUrl('submit', [
                        'record' => $record,
                    ])),
            ]);
    }
}
