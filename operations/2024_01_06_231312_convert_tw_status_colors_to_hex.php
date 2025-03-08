<?php

declare(strict_types=1);

use App\Models\Status;
use App\Models\Tenant;
use TimoKoerber\LaravelOneTimeOperations\OneTimeOperation;

return new class extends OneTimeOperation
{
    public function process(): void
    {
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
