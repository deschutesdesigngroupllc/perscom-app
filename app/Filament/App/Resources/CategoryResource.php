<?php

declare(strict_types=1);

namespace App\Filament\App\Resources;

use App\Models\Category;
use BackedEnum;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class CategoryResource extends BaseResource
{
    protected static ?string $model = Category::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->helperText('The name of the category.')
                    ->required()
                    ->maxLength(255),
                Hidden::make('resource'),
            ]);
    }
}
