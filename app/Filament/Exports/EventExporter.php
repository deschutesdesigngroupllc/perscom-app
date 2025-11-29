<?php

declare(strict_types=1);

namespace App\Filament\Exports;

use App\Models\Event;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class EventExporter extends Exporter
{
    protected static ?string $model = Event::class;

    /**
     * @return ExportColumn[]
     */
    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('name'),
            ExportColumn::make('calendar.name'),
            ExportColumn::make('description'),
            ExportColumn::make('content'),
            ExportColumn::make('location'),
            ExportColumn::make('url'),
            ExportColumn::make('author.name'),
            ExportColumn::make('all_day'),
            ExportColumn::make('start'),
            ExportColumn::make('end'),
            ExportColumn::make('repeats'),
            ExportColumn::make('frequency'),
            ExportColumn::make('interval'),
            ExportColumn::make('end_type'),
            ExportColumn::make('count'),
            ExportColumn::make('until'),
            ExportColumn::make('by_day'),
            ExportColumn::make('by_month'),
            ExportColumn::make('by_set_position'),
            ExportColumn::make('by_month_day'),
            ExportColumn::make('by_year_day'),
            ExportColumn::make('rrule'),
            ExportColumn::make('registration_enabled'),
            ExportColumn::make('registration_deadline'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your event export has completed and '.number_format($export->successful_rows).' '.str('row')->plural($export->successful_rows).' exported.';

        if (($failedRowsCount = $export->getFailedRowsCount()) !== 0) {
            $body .= ' '.number_format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to export.';
        }

        return $body;
    }
}
