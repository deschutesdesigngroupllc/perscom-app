<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Filament\App\Pages\Page as FilamentPage;
use App\Models\Page;
use Filament\Exceptions\NoDefaultPanelSetException;
use Filament\Facades\Filament;
use Filament\Navigation\NavigationItem;
use Illuminate\Support\Str;
use Illuminate\Support\Uri;
use Stancl\Tenancy\Events\TenancyInitialized;

class RegisterCustomPages
{
    /**
     * @throws NoDefaultPanelSetException
     */
    public function handle(TenancyInitialized $event): void
    {
        $panel = Filament::getCurrentOrDefaultPanel();

        if (is_null($panel)) {
            return;
        }

        $pages = Page::all()->map(function (Page $page) {
            return NavigationItem::make($page->name)
                ->url(FilamentPage::getUrl(['page' => $page->slug]))
                ->isActiveWhen(fn () => Str::is(Uri::of(FilamentPage::getUrl(['page' => $page->slug]))->path(), request()->path()))
                ->group('Pages')
                ->icon($page->icon)
                ->sort($page->order)
                ->hidden($page->hidden);
        })->toArray();

        if (empty($pages)) {
            return;
        }

        $panel->navigationItems($pages);
    }
}
