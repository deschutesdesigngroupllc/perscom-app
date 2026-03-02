<?php

declare(strict_types=1);

namespace App\Filament\Concerns;

if (trait_exists('Archilex\AdvancedTables\Concerns\HasViews')) {
    trait HasViews
    {
        use \Archilex\AdvancedTables\Concerns\HasViews;
    }
} else {
    trait HasViews {}
}
