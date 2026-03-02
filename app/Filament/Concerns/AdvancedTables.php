<?php

declare(strict_types=1);

namespace App\Filament\Concerns;

if (trait_exists('Archilex\AdvancedTables\AdvancedTables')) {
    trait AdvancedTables
    {
        use \Archilex\AdvancedTables\AdvancedTables;
    }
} else {
    trait AdvancedTables {}
}
