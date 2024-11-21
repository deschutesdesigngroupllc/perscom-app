<?php

declare(strict_types=1);

namespace App\Traits\Filament;

use Filament\Forms\Components\Field;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;

/**
 * @mixin EditRecord
 */
trait InteractsWithFields
{
    public static function getFormSchemaFromFields($record): array
    {
        $fields = [];

        // @phpstan-ignore-next-line
        if (is_null($record) || blank($record->fields)) {
            return $fields;
        }

        foreach ($record->fields as $field) {
            /** @var Field $class */
            $class = $field->type->getFilamentField();

            $filamentField = $class::make("data.$field->key")
                ->label($field->name)
                ->hidden($field->hidden)
                ->rules($field->rules ?? [])
                ->helperText($field->help)
                ->required($field->required);

            if ($field->type->value === 'select' && $filamentField instanceof Select) {
                $filamentField = $filamentField->options($field->options);
            }

            if ($field->type->value === 'email' && $filamentField instanceof TextInput) {
                $filamentField = $filamentField
                    ->email();
            }

            if ($field->type->value === 'number' && $filamentField instanceof TextInput) {
                $filamentField = $filamentField
                    ->numeric();
            }

            if ($field->type->value === 'password' && $filamentField instanceof TextInput) {
                $filamentField = $filamentField
                    ->password()
                    ->revealable();
            }

            if ($field->type->value === 'country' && $filamentField instanceof Select) {
                // TODO: Add countries
                //                    $filamentField = $filamentField
                //                        ->options();
            }

            if ($field->type->value === 'timezone' && $filamentField instanceof Select) {
                $filamentField = $filamentField
                    ->options(collect(timezone_identifiers_list())->mapWithKeys(fn ($timezone) => [$timezone => $timezone]));
            }

            if ($field->type->value === 'file' && $filamentField instanceof FileUpload) {
                $filamentField = $filamentField
                    ->previewable()
                    ->openable()
                    ->downloadable()
                    ->visibility('public');
            }

            $fields[] = $filamentField;
        }

        return $fields;
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
