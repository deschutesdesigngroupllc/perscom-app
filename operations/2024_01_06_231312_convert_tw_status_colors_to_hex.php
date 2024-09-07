<?php

declare(strict_types=1);

use App\Models\Status;
use App\Models\Tenant;
use TimoKoerber\LaravelOneTimeOperations\OneTimeOperation;

return new class extends OneTimeOperation
{
    public function process(): void
    {
        $tailwindMatcher = [
            'text-black' => '#000000',
            'bg-white' => '#ffffff',
            'bg-sky-100' => '#e0f2fe',
            'text-sky-600' => '#0284c7',
            'bg-gray-100' => '#f3f4f6',
            'text-gray-600' => '#4b5563',
            'bg-green-100' => '#dcfce7',
            'text-green-600' => '#16a34a',
            'bg-red-100' => '#fee2e2',
            'text-red-600' => '#dc2626',
            'bg-yellow-100' => '#fef9c3',
            'text-yellow-600' => '#ca8a04',
        ];

        //        tenancy()->runForMultiple(Tenant::all(), function ($tenant) use ($tailwindMatcher) {
        //            $status = Status::query()->get();
        //
        //            $status->each(function (Status $status) use ($tailwindMatcher) {
        //                $existingColor = $status->text_color;
        //
        //                [$color1, $color2] = rescue(function () use ($existingColor) {
        //                    [$color1, $color2] = explode(' ', $existingColor);
        //
        //                    return [$color1, $color2];
        //                }, fn () => ['bg-sky-100', 'text-sky-600']);
        //
        //                $textColor = $tailwindMatcher[$color2] ?? '#4b5563';
        //                $bgColor = $tailwindMatcher[$color1] ?? '#f3f4f6';
        //
        //                $status->forceFill([
        //                    'text_color' => $textColor,
        //                    'bg_color' => $bgColor,
        //                ])->save();
        //            });
        //        });
    }
};
