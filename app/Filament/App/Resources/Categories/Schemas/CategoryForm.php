<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\Categories\Schemas;

use App\Filament\App\Resources\Categories\Pages\CreateCategory;
use App\Filament\App\Resources\Categories\Pages\EditCategory;
use App\Models\Award;
use App\Models\Competency;
use App\Models\Document;
use App\Models\Form;
use App\Models\Position;
use App\Models\Qualification;
use App\Models\Rank;
use App\Models\Specialty;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make()
                    ->columnSpanFull()
                    ->persistTabInQueryString()
                    ->tabs([
                        Tabs\Tab::make('Category')
                            ->icon(Heroicon::OutlinedTag)
                            ->schema([
                                TextInput::make('name')
                                    ->helperText('The name of the category.')
                                    ->required()
                                    ->maxLength(255),
                                RichEditor::make('description')
                                    ->extraInputAttributes(['style' => 'min-height: 10rem;'])
                                    ->helperText('A brief description of the category.')
                                    ->nullable()
                                    ->maxLength(65535)
                                    ->columnSpanFull(),
                                Select::make('resource')
                                    ->helperText('The resource this category can be used with.')
                                    ->required()
                                    ->dehydrated()
                                    ->disabled(fn (Page $livewire): bool => ! in_array($livewire::class, [EditCategory::class, CreateCategory::class]))
                                    ->default(fn (Page $livewire): string => $livewire->getModel())
                                    ->options([
                                        Award::class => 'Awards',
                                        Competency::class => 'Competencies',
                                        Document::class => 'Documents',
                                        Form::class => 'Forms',
                                        Position::class => 'Positions',
                                        Qualification::class => 'Qualifications',
                                        Rank::class => 'Ranks',
                                        Specialty::class => 'Specialties',
                                    ]),
                            ]),
                    ]),
            ]);
    }
}
