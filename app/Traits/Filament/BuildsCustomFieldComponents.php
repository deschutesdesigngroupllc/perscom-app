<?php

declare(strict_types=1);

namespace App\Traits\Filament;

use App\Models\Field as FieldModel;
use Filament\Forms\Components\Field as FieldComponent;
use Filament\Infolists\Components\Entry as FieldEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Component;
use Filament\Support\Colors\Color;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

trait BuildsCustomFieldComponents
{
    public static function buildCustomFieldInputs(Collection $fields): array
    {
        if ($fields->isEmpty()) {
            return Arr::wrap(TextEntry::make('no_fields')
                ->color(Color::Gray)
                ->hiddenLabel()
                ->columnSpanFull()
                ->getStateUsing(fn (): string => 'There are no custom fields assigned to this resource.'));
        }

        return $fields
            ->map(fn (FieldModel $field): FieldComponent|Component => $field->type->getFilamentField('data.'.$field->key, $field))
            ->filter()
            ->toArray();
    }

    public static function buildCustomFieldEntries(Collection $fields): array
    {
        if ($fields->isEmpty()) {
            return Arr::wrap(TextEntry::make('no_fields')
                ->color(Color::Gray)
                ->hiddenLabel()
                ->columnSpanFull()
                ->getStateUsing(fn (): string => 'There are no custom fields assigned to this resource.'));
        }

        return $fields->map(fn (FieldModel $field): ?FieldEntry => $field->type->getFilamentEntry($field->key, $field))
            ->filter()
            ->toArray();
    }
}
