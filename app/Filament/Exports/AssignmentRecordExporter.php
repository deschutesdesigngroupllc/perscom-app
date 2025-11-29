<?php

declare(strict_types=1);

namespace App\Filament\Exports;

use App\Models\AssignmentRecord;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class AssignmentRecordExporter extends Exporter
{
    protected static ?string $model = AssignmentRecord::class;

    /**
     * @return ExportColumn[]
     */
    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('user.name'),
            ExportColumn::make('status.name'),
            ExportColumn::make('unit.name'),
            ExportColumn::make('position.name'),
            ExportColumn::make('specialty.name'),
            ExportColumn::make('document.name'),
            ExportColumn::make('author.name'),
            ExportColumn::make('type')
                ->formatStateUsing(fn ($state) => $state?->value),
            ExportColumn::make('text'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your assignment record export has completed and '.number_format($export->successful_rows).' '.str('row')->plural($export->successful_rows).' exported.';

        if (($failedRowsCount = $export->getFailedRowsCount()) !== 0) {
            $body .= ' '.number_format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to export.';
        }

        return $body;
    }
}
