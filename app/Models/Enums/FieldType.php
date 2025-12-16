<?php

declare(strict_types=1);

namespace App\Models\Enums;

use App\Models\Country;
use App\Models\Field;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\CodeEditor;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Field as FieldComponent;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\CodeEntry;
use Filament\Infolists\Components\ColorEntry;
use Filament\Infolists\Components\Entry as FieldEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Str;

enum FieldType: string implements HasColor, HasLabel
{
    case FIELD_BOOLEAN = 'boolean';
    case FIELD_CODE = 'code';
    case FIELD_COLOR = 'color';
    case FIELD_COUNTRY = 'country';
    case FIELD_DATE = 'date';
    case FIELD_DATETIME = 'datetime-local';
    case FIELD_EMAIL = 'email';
    case FIELD_FILE = 'file';
    case FIELD_NUMBER = 'number';
    case FIELD_PASSWORD = 'password';
    case FIELD_SELECT = 'select';
    case FIELD_TEXT = 'text';
    case FIELD_TEXTAREA = 'textarea';
    case FIELD_TIMEZONE = 'timezone';

    public function getLabel(): ?string
    {
        return match ($this) {
            FieldType::FIELD_DATETIME => 'Datetime',
            default => Str::title($this->value),
        };
    }

    public function getColor(): string|array|null
    {
        return 'gray';
    }

    public function getCast(): string
    {
        return match ($this) {
            FieldType::FIELD_BOOLEAN => 'boolean',
            FieldType::FIELD_DATE => 'date',
            FieldType::FIELD_DATETIME => 'datetime',
            FieldType::FIELD_NUMBER => 'integer',
            default => 'string',
        };
    }

    public function getField(): string
    {
        return match ($this) {
            FieldType::FIELD_BOOLEAN => 'Boolean',
            FieldType::FIELD_CODE => 'Code',
            FieldType::FIELD_COLOR => 'Color',
            FieldType::FIELD_COUNTRY => 'Country',
            FieldType::FIELD_DATE => 'Date',
            FieldType::FIELD_DATETIME => 'Datetime',
            FieldType::FIELD_EMAIL => 'Email',
            FieldType::FIELD_FILE => 'File',
            FieldType::FIELD_NUMBER => 'Number',
            FieldType::FIELD_PASSWORD => 'Password',
            FieldType::FIELD_SELECT => 'Select',
            FieldType::FIELD_TEXT => 'Text',
            FieldType::FIELD_TEXTAREA => 'Textarea',
            FieldType::FIELD_TIMEZONE => 'Timezone',
        };
    }

    public function getFilamentField(string $name, Field $field): FieldComponent
    {
        $filament = match ($this) {
            FieldType::FIELD_BOOLEAN => Checkbox::make($name),
            FieldType::FIELD_CODE => CodeEditor::make($name),
            FieldType::FIELD_TEXTAREA => Textarea::make($name),
            FieldType::FIELD_COLOR => ColorPicker::make($name),
            FieldType::FIELD_SELECT => Select::make($name)
                ->options($field->options)
                ->preload()
                ->searchable(),
            FieldType::FIELD_COUNTRY => Select::make($name)
                ->preload()
                ->searchable()
                ->options(Country::query()->orderBy('official_name')->pluck('official_name', 'official_name')->toArray()),
            FieldType::FIELD_TIMEZONE => Select::make($name)
                ->preload()
                ->searchable()
                ->options(collect(timezone_identifiers_list())->mapWithKeys(fn ($timezone): array => [$timezone => $timezone])),
            FieldType::FIELD_DATE => DatePicker::make($name),
            FieldType::FIELD_DATETIME => DateTimePicker::make($name),
            FieldType::FIELD_TEXT => TextInput::make($name),
            FieldType::FIELD_EMAIL => TextInput::make($name)
                ->autocomplete('email')
                ->email(),
            FieldType::FIELD_NUMBER => TextInput::make($name)
                ->numeric(),
            FieldType::FIELD_FILE => FileUpload::make($name)
                ->previewable()
                ->openable()
                ->downloadable()
                ->visibility('public'),
            FieldType::FIELD_PASSWORD => TextInput::make($name)
                ->autocomplete('current-password')
                ->revealable()
                ->password(),
        };

        return $filament
            ->label($field->name)
            ->hidden($field->hidden)
            ->rules($field->rules ?? [])
            ->helperText($field->help)
            ->default($field->default)
            ->required($field->required);
    }

    public function getFilamentEntry(string $name, Field $field): FieldEntry
    {
        $filament = match ($this) {
            FieldType::FIELD_BOOLEAN => TextEntry::make($name)->badge(),
            FieldType::FIELD_CODE => CodeEntry::make($name),
            FieldType::FIELD_COUNTRY, FieldType::FIELD_EMAIL, FieldType::FIELD_FILE, FieldType::FIELD_NUMBER, FieldType::FIELD_PASSWORD, FieldType::FIELD_TEXTAREA, FieldType::FIELD_TEXT, FieldType::FIELD_TIMEZONE, FieldType::FIELD_SELECT => TextEntry::make($name),
            FieldType::FIELD_DATE => TextEntry::make($name)->date(),
            FieldType::FIELD_DATETIME => TextEntry::make($name)->dateTime(),
            FieldType::FIELD_COLOR => ColorEntry::make($name),
        };

        return $filament->label($field->name);
    }
}
