<?php

declare(strict_types=1);

namespace App\Traits\Filament;

use App\Models\Field as FieldModel;
use Filament\Forms\Components\Field as FieldComponent;
use Filament\Infolists\Components\Entry as FieldEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Support\Colors\Color;
use Illuminate\Support\Arr;

trait InteractsWithFields
{
    public static function getFormSchemaFromFields($record): array
    {
        // @phpstan-ignore-next-line
        if (is_null($record) || blank($record->fields)) {
            return Arr::wrap(TextEntry::make('no_fields')
                ->color(Color::Gray)
                ->hiddenLabel()
                ->columnSpanFull()
                ->getStateUsing(fn (): string => 'There are no custom fields assigned to this resource.'));
        }

        return $record->fields->map(fn (FieldModel $field): FieldComponent => $field->type->getFilamentField('data.'.$field->key, $field))->toArray();
    }

    public static function getInfolistSchemaFromFields($record): array
    {
        // @phpstan-ignore-next-line
        if (is_null($record) || blank($record->fields)) {
            return Arr::wrap(TextEntry::make('no_fields')
                ->color(Color::Gray)
                ->hiddenLabel()
                ->columnSpanFull()
                ->getStateUsing(fn (): string => 'There are no custom fields assigned to this resource.'));
        }

        return $record->fields->map(fn (FieldModel $field): FieldEntry => $field->type->getFilamentEntry($field->key))->toArray();
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (! method_exists(parent::class, 'mutateFormDataBeforeSave')) {
            return $data;
        }

        if ($fields = data_get($data, 'data')) {
            $record = $this->getRecord();
            $record->forceFill($fields)->save();

            data_forget($data, 'data');
        }

        return parent::mutateFormDataBeforeSave($data);
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        if (! method_exists(parent::class, 'mutateFormDataBeforeFill')) {
            return $data;
        }

        $record = $this->getRecord();

        return parent::mutateFormDataBeforeFill(array_merge($data, [
            'data' => $record->getOriginal('data'),
        ]));
    }
}
