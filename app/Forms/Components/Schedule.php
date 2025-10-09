<?php

declare(strict_types=1);

namespace App\Forms\Components;

use App\Models\Enums\ScheduleEndType;
use App\Models\Enums\ScheduleFrequency;
use App\Models\Schedule as ScheduleModel;
use App\Services\ScheduleService;
use App\Services\UserSettingsService;
use App\Settings\OrganizationSettings;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class Schedule
{
    public static function make(bool $startHidden = false, bool $allDay = false, bool $shiftScheduleTimezone = false): Section
    {
        if (! $startHidden) {
            $start = DateTimePicker::make('start')
                ->timezone(UserSettingsService::get('timezone', function () {
                    /** @var OrganizationSettings $settings */
                    $settings = app(OrganizationSettings::class);

                    return $settings->timezone ?? config('app.timezone');
                }))
                ->live()
                ->default(now()->addHour()->startOfHour())
                ->required()
                ->columnSpanFull()
                ->helperText('Set a time the schedule should start.');
        } else {
            $start = Hidden::make('start')
                ->default(now()->addHour()->startOfHour());
        }

        return Section::make()
            ->relationship('schedule')
            ->columns(3)
            ->schema([
                $start,
                Hidden::make('duration')
                    ->default(1),
                Grid::make()
                    ->columnSpanFull()
                    ->columns(3)
                    ->schema([
                        TextInput::make('interval')
                            ->live()
                            ->helperText('The interval at which the schedule will repeat.')
                            ->numeric()
                            ->required()
                            ->rules('gt:0')
                            ->default(1),
                        Select::make('frequency')
                            ->live()
                            ->helperText('The frequency at which the schedule will repeat.')
                            ->required()
                            ->options(ScheduleFrequency::class)
                            ->default('WEEKLY')
                            ->columnSpan(fn ($state): int => $state === 'DAILY' ? 2 : 1),
                        Select::make('by_day')
                            ->live()
                            ->helperText('The day(s) of the week the schedule will repeat.')
                            ->requiredIf('frequency', 'WEEKLY')
                            ->multiple()
                            ->label('On')
                            ->default('never')
                            ->default(fn (): array => [Str::of(today()->format('D'))->substr(0, 2)->upper()->toString()])
                            ->options([
                                'SU' => 'Sunday',
                                'MO' => 'Monday',
                                'TU' => 'Tuesday',
                                'WE' => 'Wednesday',
                                'TH' => 'Thursday',
                                'FR' => 'Friday',
                                'SA' => 'Saturday',
                            ])
                            ->hidden(fn (Get $get): bool => in_array($get('frequency'), ['DAILY', 'MONTHLY', 'YEARLY'], true)),
                        Select::make('by_month_day')
                            ->live()
                            ->helperText('The day of the month the schedule will repeat.')
                            ->requiredIf('frequency', 'MONTHLY')
                            ->multiple()
                            ->label('On')
                            ->default('never')
                            ->options(function (Get $get) {
                                $month = Carbon::parse($get('start'))->startOfMonth();
                                $period = collect($month->toPeriod($month->copy()->endOfMonth(), 1, 'day')->settings([
                                    'monthOverflow' => false,
                                ]));

                                return $period->mapWithKeys(fn ($value): array => [$value->format('j') => $value->format('jS')]);
                            })
                            ->hidden(fn (Get $get): bool => in_array($get('frequency'), ['DAILY', 'WEEKLY', 'YEARLY'], true)),
                        Select::make('by_month')
                            ->live()
                            ->helperText('The month of the year the schedule will repeat.')
                            ->multiple()
                            ->label('On')
                            ->default('never')
                            ->options([
                                '1' => 'January',
                                '2' => 'February',
                                '3' => 'March',
                                '4' => 'April',
                                '5' => 'May',
                                '6' => 'June',
                                '7' => 'July',
                                '8' => 'August',
                                '9' => 'September',
                                '10' => 'October',
                                '11' => 'November',
                                '12' => 'December',
                            ])
                            ->hidden(fn (Get $get): bool => in_array($get('frequency'), ['DAILY', 'WEEKLY', 'MONTHLY'], true)),
                    ]),
                Select::make('end_type')
                    ->live()
                    ->helperText('When the schedule will end.')
                    ->label('Ends')
                    ->default('never')
                    ->options(ScheduleEndType::class)
                    ->columnSpan(fn ($state): int => $state !== 'never' ? 1 : 3),
                TextInput::make('count')
                    ->live()
                    ->columnSpan(2)
                    ->default(1)
                    ->label('Occurrences')
                    ->helperText('The number of occurrences before the recurring schedule ends.')
                    ->hidden(fn (Get $get): bool => $get('end_type') !== 'after')
                    ->required(fn (Get $get): bool => $get('end_type') === 'after')
                    ->numeric(),
                DatePicker::make('until')
                    ->live()
                    ->columnSpan(2)
                    ->default(today()->addMonth())
                    ->label('End Date')
                    ->helperText('The date the recurring schedule will end.')
                    ->hidden(fn (Get $get): bool => ScheduleEndType::from($get('end_type')) !== ScheduleEndType::ON)
                    ->required(fn (Get $get): bool => ScheduleEndType::from($get('end_type')) === ScheduleEndType::ON)
                    ->dehydrateStateUsing(function ($state, Get $get): Carbon {
                        $start = Carbon::parse($get('start'));
                        $until = Carbon::parse($state);

                        return $until->setTimeFrom($start);
                    }),
                Placeholder::make('schedule')
                    ->helperText('The configured schedule will repeat using the pattern above.')
                    ->columnSpanFull()
                    ->content(function (Get $get) use ($allDay, $shiftScheduleTimezone): string {
                        $start = Carbon::parse($get('start'));

                        if ($shiftScheduleTimezone) {
                            $start->shiftTimezone(UserSettingsService::get('timezone', function () {
                                /** @var OrganizationSettings $settings */
                                $settings = app(OrganizationSettings::class);

                                return $settings->timezone ?? config('app.timezone');
                            }))
                                ->setTimezone(config('app.timezone'));
                        }

                        $schedule = new ScheduleModel([
                            'start' => $start,
                            'frequency' => $get('frequency'),
                            'interval' => $get('interval'),
                            'end_type' => $get('end_type'),
                            'count' => $get('count'),
                            'until' => $get('until'),
                            'by_day' => $get('by_day'),
                            'by_month_day' => $get('by_month_day'),
                            'by_month' => $get('by_month'),
                        ]);

                        return ScheduleService::getSchedulePattern($schedule, $allDay) ?? '---';
                    }),
            ]);
    }
}
