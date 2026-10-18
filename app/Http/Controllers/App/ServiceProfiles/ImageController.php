<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\ServiceProfiles;

use App\Models\User;
use App\Services\ServiceProfileImageService;
use App\Services\ServiceProfileService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class ImageController
{
    public function __invoke(string $handle): Response
    {
        $member = ServiceProfileService::findByHandle($handle);

        abort_unless($member instanceof User && ServiceProfileService::isPublic($member), 404);

        $png = Cache::remember(
            sprintf('service-profile-image:%s:%s', $member->getKey(), $member->updated_at?->getTimestamp()),
            now()->addDay(),
            fn (): string => base64_encode(ServiceProfileImageService::render(ServiceProfileService::build($member))),
        );

        return response(base64_decode((string) $png), 200, [
            'Content-Type' => 'image/png',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }
}
