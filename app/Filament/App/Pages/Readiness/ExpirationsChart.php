<?php

declare(strict_types=1);

namespace App\Filament\App\Pages\Readiness;

use App\Models\QualificationRecord;
use App\Models\TrainingRecordCredential;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Support\Collection;

class ExpirationsChart extends ChartWidget
{
    protected ?string $heading = 'Expirations By Month';

    protected ?string $description = 'Qualifications and credentials that expired or will expire, from six months ago to six months ahead.';

    protected int|string|array $columnSpan = 'full';

    protected ?string $maxHeight = '250px';

    protected function getData(): array
    {
        $qualifications = $this->trend(QualificationRecord::class);
        $credentials = $this->trend(TrainingRecordCredential::class);

        return [
            'datasets' => [
                [
                    'label' => 'Qualifications',
                    'data' => $qualifications->map(fn (TrendValue $value): mixed => $value->aggregate),
                    'backgroundColor' => '#f59e0b',
                ],
                [
                    'label' => 'Credentials',
                    'data' => $credentials->map(fn (TrendValue $value): mixed => $value->aggregate),
                    'backgroundColor' => '#0ea5e9',
                ],
            ],
            'labels' => $qualifications->map(fn (TrendValue $value): string => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    /**
     * @param  class-string<QualificationRecord|TrainingRecordCredential>  $model
     * @return Collection<int, TrendValue>
     */
    protected function trend(string $model): Collection
    {
        return Trend::query($model::query()->withoutGlobalScopes()->whereNotNull('expires_at'))
            ->dateColumn('expires_at')
            ->between(
                start: now()->subMonths(6)->startOfMonth(),
                end: now()->addMonths(6)->endOfMonth(),
            )
            ->perMonth()
            ->count();
    }
}
