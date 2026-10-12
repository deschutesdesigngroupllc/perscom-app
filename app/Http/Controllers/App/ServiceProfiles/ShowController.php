<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\ServiceProfiles;

use App\Models\User;
use App\Services\ServiceProfileService;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentColor;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class ShowController
{
    public function __invoke(string $handle): View|RedirectResponse
    {
        $member = ServiceProfileService::findByHandle($handle);

        abort_unless($member instanceof User && ServiceProfileService::isPublic($member), 404);

        if ($handle !== ServiceProfileService::handle($member)) {
            return redirect()->to(ServiceProfileService::url($member), 301);
        }

        FilamentColor::register([
            'primary' => Color::Blue,
        ]);

        return view('service-profiles.show', [
            'profile' => ServiceProfileService::build($member),
        ]);
    }
}
