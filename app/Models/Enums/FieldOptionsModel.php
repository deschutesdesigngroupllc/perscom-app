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
    case Awards = 'awards';
    case Calendars = 'calendars';
    case Events = 'events';
    case Forms = 'forms';
    case Groups = 'groups';
    case Documents = 'documents';
    case Positions = 'positions';
    case Qualifications = 'qualifications';
    case Ranks = 'ranks';
    case Specialties = 'specialties';
    case Statuses = 'statuses';
    case Tasks = 'tasks';
    case Slots = 'slots';
    case Units = 'units';
    case Users = 'users';

    public function getLabel(): string
    {
        return Str::title($this->value);
    }

    public function getOptions(): array
    {
        return match ($this) {
            FieldOptionsModel::Awards => Award::pluck('name', 'name')->toArray(),
            FieldOptionsModel::Calendars => Calendar::orderBy('name')->pluck('name', 'name')->toArray(),
            FieldOptionsModel::Events => Event::orderBy('name')->pluck('name', 'name')->toArray(),
            FieldOptionsModel::Forms => Form::orderBy('name')->pluck('name', 'name')->toArray(),
            FieldOptionsModel::Groups => Group::pluck('name', 'name')->toArray(),
            FieldOptionsModel::Documents => Document::orderBy('name')->pluck('name', 'name')->toArray(),
            FieldOptionsModel::Positions => Position::pluck('name', 'name')->toArray(),
            FieldOptionsModel::Qualifications => Qualification::pluck('name', 'name')->toArray(),
            FieldOptionsModel::Ranks => Rank::pluck('name', 'name')->toArray(),
            FieldOptionsModel::Specialties => Specialty::pluck('name', 'name')->toArray(),
            FieldOptionsModel::Statuses => Status::pluck('name', 'name')->toArray(),
            FieldOptionsModel::Tasks => Task::orderBy('title')->pluck('title', 'title')->toArray(),
            FieldOptionsModel::Slots => Slot::pluck('name', 'name')->toArray(),
            FieldOptionsModel::Units => Unit::pluck('name', 'name')->toArray(),
            FieldOptionsModel::Users => User::orderBy('name')->pluck('name', 'name')->toArray(),
        };
    }
}
