<?php

declare(strict_types=1);

namespace App\Models\Enums;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\ColorEntry;
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

    public function getFilamentField(): string
    {
        return match ($this) {
            FieldType::FIELD_BOOLEAN => Checkbox::class,
            FieldType::FIELD_CODE, FieldType::FIELD_TEXTAREA => Textarea::class,
            FieldType::FIELD_COLOR => ColorPicker::class,
            FieldType::FIELD_COUNTRY, FieldType::FIELD_SELECT, FieldType::FIELD_TIMEZONE => Select::class,
            FieldType::FIELD_DATE => DatePicker::class,
            FieldType::FIELD_DATETIME => DateTimePicker::class,
            FieldType::FIELD_EMAIL, FieldType::FIELD_NUMBER, FieldType::FIELD_PASSWORD, FieldType::FIELD_TEXT => TextInput::class,
            FieldType::FIELD_FILE => FileUpload::class,
        };
    }

    public function getFilamentEntry(): string
    {
        return match ($this) {
            FieldType::FIELD_BOOLEAN, FieldType::FIELD_CODE, FieldType::FIELD_COUNTRY, FieldType::FIELD_DATE, FieldType::FIELD_DATETIME, FieldType::FIELD_EMAIL, FieldType::FIELD_FILE, FieldType::FIELD_NUMBER, FieldType::FIELD_PASSWORD, FieldType::FIELD_SELECT, FieldType::FIELD_TEXTAREA, FieldType::FIELD_TEXT, FieldType::FIELD_TIMEZONE => TextEntry::class,
            FieldType::FIELD_COLOR => ColorEntry::class,
        };
    }
}
