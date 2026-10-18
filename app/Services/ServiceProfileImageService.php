<?php

declare(strict_types=1);

namespace App\Services;

use App\Data\ServiceProfiles\ServiceProfileData;
use App\Models\AwardRecord;
use App\Models\Image;
use GdImage;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

/**
 * Renders the 1200×630 share preview shown when a service profile link is posted to Discord,
 * Slack, Reddit or X. Only raster insignia and ribbons can be drawn; SVG artwork is skipped.
 */
class ServiceProfileImageService
{
    public const int WIDTH = 1200;

    public const int HEIGHT = 630;

    protected const int PADDING = 72;

    public static function render(ServiceProfileData $profile): string
    {
        $canvas = imagecreatetruecolor(self::WIDTH, self::HEIGHT);
        imagealphablending($canvas, true);
        imagesavealpha($canvas, true);

        ServiceProfileImageService::background($canvas);

        $user = $profile->user;
        $left = self::PADDING;

        $insignia = $profile->showsRank ? ServiceProfileImageService::load($user->rank?->image) : null;

        if ($insignia instanceof GdImage) {
            ServiceProfileImageService::paste($canvas, $insignia, $left, 150, 150, 150);
            $left += 190;
        } elseif ($profile->showsRank && filled($user->rank?->abbreviation)) {
            ServiceProfileImageService::badge($canvas, (string) $user->rank->abbreviation, $left, 165);
            $left += 190;
        }

        $textWidth = self::WIDTH - $left - self::PADDING;

        if ($profile->showsRank && filled($user->rank?->name)) {
            ServiceProfileImageService::text($canvas, Str::upper((string) $user->rank->name), 22, $left, 178, '#60a5fa', bold: true, maxWidth: $textWidth);
        }

        ServiceProfileImageService::text($canvas, $user->name, 64, $left, 252, '#ffffff', bold: true, maxWidth: $textWidth);

        $assignment = $profile->showsAssignment
            ? collect([$user->position?->name, $user->unit?->name])->filter()->implode('  ·  ')
            : null;

        if (filled($assignment)) {
            ServiceProfileImageService::text($canvas, $assignment, 26, $left, 302, '#cbd5e1', maxWidth: $textWidth);
        }

        ServiceProfileImageService::ribbons($canvas, $profile, self::PADDING, 372);

        $stats = collect([
            filled($profile->timeInService) ? $profile->timeInService.' in service' : null,
            $profile->awards->isNotEmpty() ? $profile->awards->count().' '.Str::plural('award', $profile->awards->count()) : null,
            $profile->qualifications->isNotEmpty() ? $profile->qualifications->count().' '.Str::plural('qualification', $profile->qualifications->count()) : null,
        ])->filter()->implode('   ·   ');

        if (filled($stats)) {
            ServiceProfileImageService::text($canvas, $stats, 24, self::PADDING, 490, '#94a3b8', maxWidth: self::WIDTH - (self::PADDING * 2));
        }

        imagefilledrectangle($canvas, 0, 548, self::WIDTH, self::HEIGHT, ServiceProfileImageService::color($canvas, '#020617'));
        ServiceProfileImageService::text($canvas, $profile->organization, 24, self::PADDING, 600, '#e2e8f0', bold: true, maxWidth: 700);
        ServiceProfileImageService::text($canvas, 'PERSCOM', 24, self::WIDTH - self::PADDING - ServiceProfileImageService::width('PERSCOM', 24, true), 600, '#60a5fa', bold: true);

        ob_start();
        imagepng($canvas, null, 6);
        $png = (string) ob_get_clean();

        imagedestroy($canvas);

        return $png;
    }

    protected static function background(GdImage $canvas): void
    {
        $top = [15, 23, 42];
        $bottom = [2, 6, 23];

        collect(range(0, self::HEIGHT - 1))->each(function (int $y) use ($canvas, $top, $bottom): void {
            $ratio = $y / self::HEIGHT;
            $color = imagecolorallocate(
                $canvas,
                (int) ($top[0] + ($bottom[0] - $top[0]) * $ratio),
                (int) ($top[1] + ($bottom[1] - $top[1]) * $ratio),
                (int) ($top[2] + ($bottom[2] - $top[2]) * $ratio),
            );
            imageline($canvas, 0, $y, self::WIDTH, $y, $color);
        });

        imagefilledrectangle($canvas, 0, 0, self::WIDTH, 8, ServiceProfileImageService::color($canvas, '#2563eb'));
    }

    protected static function ribbons(GdImage $canvas, ServiceProfileData $profile, int $x, int $y): void
    {
        $height = 40;
        $maxX = self::WIDTH - self::PADDING;

        $profile->awards
            ->map(fn (AwardRecord $record): ?GdImage => ServiceProfileImageService::load($record->award?->image))
            ->filter()
            ->each(function (GdImage $ribbon) use ($canvas, &$x, $y, $height, $maxX): bool {
                $width = (int) round(imagesx($ribbon) * ($height / max(1, imagesy($ribbon))));

                if ($x + $width > $maxX) {
                    return false;
                }

                ServiceProfileImageService::paste($canvas, $ribbon, $x, $y, $width, $height);
                $x += $width + 6;

                return true;
            });
    }

    protected static function badge(GdImage $canvas, string $label, int $x, int $y): void
    {
        imagefilledrectangle($canvas, $x, $y, $x + 150, $y + 120, ServiceProfileImageService::color($canvas, '#1e293b'));
        imagerectangle($canvas, $x, $y, $x + 150, $y + 120, ServiceProfileImageService::color($canvas, '#334155'));

        $size = 40;
        while ($size > 14 && ServiceProfileImageService::width($label, $size, true) > 130) {
            $size -= 2;
        }

        ServiceProfileImageService::text($canvas, $label, $size, $x + (int) ((150 - ServiceProfileImageService::width($label, $size, true)) / 2), $y + 60 + (int) ($size / 2), '#ffffff', bold: true);
    }

    protected static function text(GdImage $canvas, string $text, int $size, int $x, int $y, string $color, bool $bold = false, ?int $maxWidth = null): void
    {
        if ($maxWidth !== null) {
            while ($size > 14 && ServiceProfileImageService::width($text, $size, $bold) > $maxWidth) {
                $size -= 2;
            }

            while (mb_strlen($text) > 1 && ServiceProfileImageService::width($text, $size, $bold) > $maxWidth) {
                $text = rtrim(mb_substr($text, 0, -2)).'…';
            }
        }

        imagettftext($canvas, $size, 0, $x, $y, ServiceProfileImageService::color($canvas, $color), ServiceProfileImageService::font($bold), $text);
    }

    protected static function width(string $text, int $size, bool $bold = false): int
    {
        $box = imagettfbbox($size, 0, ServiceProfileImageService::font($bold), $text);

        return $box === false ? 0 : abs($box[2] - $box[0]);
    }

    protected static function font(bool $bold): string
    {
        return resource_path($bold ? 'fonts/Inter-Bold.ttf' : 'fonts/Inter-Regular.ttf');
    }

    protected static function color(GdImage $canvas, string $hex): int
    {
        [$red, $green, $blue] = sscanf(ltrim($hex, '#'), '%02x%02x%02x');

        return (int) imagecolorallocate($canvas, (int) $red, (int) $green, (int) $blue);
    }

    protected static function paste(GdImage $canvas, GdImage $source, int $x, int $y, int $maxWidth, int $maxHeight): void
    {
        $scale = min($maxWidth / max(1, imagesx($source)), $maxHeight / max(1, imagesy($source)));
        $width = (int) round(imagesx($source) * $scale);
        $height = (int) round(imagesy($source) * $scale);

        imagecopyresampled(
            $canvas,
            $source,
            $x + (int) (($maxWidth - $width) / 2),
            $y + (int) (($maxHeight - $height) / 2),
            0,
            0,
            $width,
            $height,
            imagesx($source),
            imagesy($source),
        );
    }

    protected static function load(?Image $image): ?GdImage
    {
        if (! $image instanceof Image || blank($image->path) || Str::endsWith(Str::lower($image->path), '.svg')) {
            return null;
        }

        try {
            $contents = Storage::get($image->path);
            $source = filled($contents) ? @imagecreatefromstring($contents) : false;
        } catch (Throwable) {
            return null;
        }

        return $source instanceof GdImage ? $source : null;
    }
}
