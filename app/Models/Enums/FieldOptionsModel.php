<?php

declare(strict_types=1);

namespace App\Models\Enums;

use App\Models\Award;
use App\Models\Calendar;
use App\Models\Document;
use App\Models\Event;
use App\Models\Form;
use App\Models\Group;
use App\Models\Position;
use App\Models\Qualification;
use App\Models\Rank;
use App\Models\Slot;
use App\Models\Specialty;
use App\Models\Status;
use App\Models\Task;
use App\Models\Unit;
use App\Models\User;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Str;

enum FieldOptionsModel: string implements HasLabel
{
    case AWARDS = 'awards';
    case CALENDARS = 'calendars';
    case EVENTS = 'events';
    case FORMS = 'forms';
    case GROUPS = 'groups';
    case DOCUMENTS = 'documents';
    case POSITIONS = 'positions';
    case QUALIFICATIONS = 'qualifications';
    case RANKS = 'ranks';
    case SPECIALTIES = 'specialties';
    case STATUSES = 'statuses';
    case TASKS = 'tasks';
    case SLOTS = 'slots';
    case UNITS = 'units';
    case USERS = 'users';

    public function getLabel(): string
    {
        return Str::title($this->value);
    }

    public function getOptions(): array
    {
        return match ($this) {
            FieldOptionsModel::AWARDS => Award::pluck('name', 'name')->toArray(),
            FieldOptionsModel::CALENDARS => Calendar::orderBy('name')->pluck('name', 'name')->toArray(),
            FieldOptionsModel::EVENTS => Event::orderBy('name')->pluck('name', 'name')->toArray(),
            FieldOptionsModel::FORMS => Form::orderBy('name')->pluck('name', 'name')->toArray(),
            FieldOptionsModel::GROUPS => Group::pluck('name', 'name')->toArray(),
            FieldOptionsModel::DOCUMENTS => Document::orderBy('name')->pluck('name', 'name')->toArray(),
            FieldOptionsModel::POSITIONS => Position::pluck('name', 'name')->toArray(),
            FieldOptionsModel::QUALIFICATIONS => Qualification::pluck('name', 'name')->toArray(),
            FieldOptionsModel::RANKS => Rank::pluck('name', 'name')->toArray(),
            FieldOptionsModel::SPECIALTIES => Specialty::pluck('name', 'name')->toArray(),
            FieldOptionsModel::STATUSES => Status::pluck('name', 'name')->toArray(),
            FieldOptionsModel::TASKS => Task::orderBy('title')->pluck('title', 'title')->toArray(),
            FieldOptionsModel::SLOTS => Slot::pluck('name', 'name')->toArray(),
            FieldOptionsModel::UNITS => Unit::pluck('name', 'name')->toArray(),
            FieldOptionsModel::USERS => User::orderBy('name')->pluck('name', 'name')->toArray(),
        };
    }
}
