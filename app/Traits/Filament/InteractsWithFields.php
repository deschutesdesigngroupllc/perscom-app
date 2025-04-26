<?php

declare(strict_types=1);

namespace App\Traits\Filament;

use App\Models\Enums\FieldOptionsModel;
use App\Models\Enums\FieldOptionsType;
use App\Models\Enums\FieldType;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Arr;

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
            return Arr::wrap(Placeholder::make('no_fields')
                ->hiddenLabel()
                ->content('There are no custom fields assigned to this resource.'));
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

            if ($field->type === FieldType::FIELD_SELECT && $filamentField instanceof Select) {
                $filamentField = match ($field->options_type) {
                    FieldOptionsType::Array => $filamentField->options($field->options),
                    FieldOptionsType::Model => $filamentField->options(optional($field->options_model, fn (FieldOptionsModel $model) => $model->getOptions()) ?? []),
                };

                $filamentField = $filamentField->searchable();
            }

            if ($field->type === FieldType::FIELD_EMAIL && $filamentField instanceof TextInput) {
                $filamentField = $filamentField
                    ->autocomplete('email')
                    ->email();
            }

            if ($field->type === FieldType::FIELD_NUMBER && $filamentField instanceof TextInput) {
                $filamentField = $filamentField
                    ->numeric();
            }

            if ($field->type === FieldType::FIELD_PASSWORD && $filamentField instanceof TextInput) {
                $filamentField = $filamentField
                    ->autocomplete('current-password')
                    ->password()
                    ->revealable();
            }

            if ($field->type === FieldType::FIELD_COUNTRY && $filamentField instanceof Select) {
                // TODO: Add countries
                //                    $filamentField = $filamentField
                //                        ->options();
            }

            if ($field->type === FieldType::FIELD_TIMEZONE && $filamentField instanceof Select) {
                $filamentField = $filamentField
                    ->searchable()
                    ->options(collect(timezone_identifiers_list())->mapWithKeys(fn ($timezone) => [$timezone => $timezone]));
            }

            if ($field->type === FieldType::FIELD_FILE && $filamentField instanceof FileUpload) {
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
