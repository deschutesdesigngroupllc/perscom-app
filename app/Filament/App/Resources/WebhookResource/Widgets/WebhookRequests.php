<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\WebhookResource\Widgets;

use App\Models\WebhookLog;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class WebhookRequests extends ChartWidget
{
    protected ?string $heading = 'Live Webhook Requests';

    protected ?string $description = 'Your account\'s webhook request history over the last year.';

    protected int|string|array $columnSpan = 'full';

    protected ?string $maxHeight = '150px';

    protected ?string $pollingInterval = '10s';

    protected ?array $options = [
        'plugins' => [
            'legend' => [
                'display' => false,
            ],
        ],
    ];

    protected function getData(): array
    {
        $data = Trend::model(WebhookLog::class)
            ->between(
                start: now()->startOfYear(),
                end: now()->endOfYear(),
            )
            ->perMonth()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Webhook requests',
                    'data' => $data->map(fn (TrendValue $value): mixed => $value->aggregate),
                    'fill' => true,
                ],
            ],
            'labels' => $data->map(fn (TrendValue $value): string => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
