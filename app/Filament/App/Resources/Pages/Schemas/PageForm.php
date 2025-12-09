<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\Pages\Schemas;

use App\Filament\App\Resources\Pages\Actions\CodeEditorAction;
use App\Models\Page;
use Filament\Forms\Components\CodeEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Guava\IconPicker\Forms\Components\IconPicker;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class PageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                TextInput::make('name')
                    ->helperText('The name of the page.')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (string $context, $state, Set $set): void {
                        if ($context === 'create') {
                            $set('slug', Str::slug($state));
                            $set('navigation_label', Str::title($state));
                        }
                    }),
                TextInput::make('slug')
                    ->helperText('The slug of the page.')
                    ->unique(ignoreRecord: true)
                    ->rules(['alpha_dash'])
                    ->maxLength(255)
                    ->required(),
                Textarea::make('description')
                    ->helperText('An optional description of the page that will be used as the subheading.')
                    ->nullable()
                    ->maxLength(65535),
                IconPicker::make('icon')
                    ->required()
                    ->helperText('The icon used for the menu item.')
                    ->columnSpanFull(),
                CodeEditor::make('content')
                    ->hintAction(fn (Page $record) => CodeEditorAction::make()->page($record))
                    ->helperText(new HtmlString("The HTML content of the page. You may use the <a class='underline' href='https://twig.symfony.com/' target='_blank'>Twig Template Engine</a> to add dynamic rendering. For security, certain functions, filters, tags, etc have been disabled. Tailwind CSS may also be used to style the page."))
                    ->language(CodeEditor\Enums\Language::Html)
                    ->required()
                    ->columnSpanFull(),
            ]);
    }
}
