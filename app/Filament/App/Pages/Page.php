<?php

declare(strict_types=1);

namespace App\Filament\App\Pages;

use App\Models\Page as PageModel;
use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Pages\Page as BasePage;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Http\Request;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Loader\ArrayLoader;

class Page extends BasePage
{
    use HasPageShield;

    public ?PageModel $page = null;

    protected ?Environment $twig = null;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBookOpen;

    protected static bool $shouldRegisterNavigation = false;

    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.app.pages.page';

    public function mount(Request $request): void
    {
        $this->twig = app('twig');
        $this->page = PageModel::query()->where('slug', $request->query('page'))->firstOrFail();
    }

    public function getHeading(): string|Htmlable|null
    {
        return $this->page->name;
    }

    public function getSubheading(): string|Htmlable|null
    {
        return $this->page->description;
    }

    /**
     * @return array<string, string>
     *
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    protected function getViewData(): array
    {
        $this->twig->setLoader(new ArrayLoader([
            'template' => '',
        ]));

        /** @var ArrayLoader $loader */
        $loader = $this->twig->getLoader();
        $loader->setTemplate('page', $this->page->content);

        return [
            'html' => $this->twig->render('page'),
        ];
    }
}
