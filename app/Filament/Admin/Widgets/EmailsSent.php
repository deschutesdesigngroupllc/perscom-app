<?php

declare(strict_types=1);

namespace App\Filament\Admin\Widgets;

use App\Metrics\EmailSentMetric;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class EmailsSent extends ChartWidget
{
    protected static ?int $sort = 5;

    protected static ?string $heading = 'Emails Sent';

    protected static ?string $description = 'The emails sent by the application.';

    protected int|string|array $columnSpan = 'full';

    protected static ?string $maxHeight = '150px';

    protected static ?string $pollingInterval = '10s';

    protected static ?array $options = [
        'plugins' => [
            'legend' => [
                'display' => false,
            ],
        ],
    ];

    protected function getData(): array
    {
        $emailsSent = new EmailSentMetric;
        $emailsSentData = Trend::query($emailsSent->query())
            ->between(
                start: now()->startOfYear(),
                end: now()->endOfYear(),
            )
            ->perMonth()
            ->sum('count');

        return [
            'datasets' => [
                [
                    'label' => 'Emails sent',
                    'data' => $emailsSentData->map(fn (TrendValue $value) => $value->aggregate),
                    'fill' => true,
                    'borderColor' => '#2563eb',
                ],
            ],
            'labels' => $emailsSentData->map(fn (TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
