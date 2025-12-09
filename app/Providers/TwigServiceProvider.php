<?php

declare(strict_types=1);

namespace App\Providers;

use App\Support\Twig\Extensions\SsoJwtExtension;
use App\Support\Twig\Extensions\WidgetUrlExtension;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\ServiceProvider;
use Twig\Environment;
use Twig\Extension\SandboxExtension;
use Twig\Loader\FilesystemLoader;
use Twig\Sandbox\SecurityPolicy;

class TwigServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton('twig', function (Container $app): Environment {
            $loader = new FilesystemLoader(resource_path('views/twig'));
            $policy = new SecurityPolicy(
                allowedTags: ['apply', 'if', 'for', 'set'],
                allowedFilters: ['capitalize', 'date', 'escape', 'find', 'first', 'format_currency', 'format_date', 'format_number', 'format_time', 'join', 'last', 'length', 'lower', 'map', 'plural', 'reduce', 'round', 'singular', 'slice', 'split', 'title', 'trim', 'upper'],
                allowedMethods: [],
                allowedProperties: [],
                allowedFunctions: []
            );

            $twig = new Environment($loader, [
                'cache' => storage_path('twig'),
                'debug' => config('app.debug'),
                'autoescape' => 'html',
            ]);

            $twig->addExtension(new SandboxExtension($policy));
            $twig->addExtension($app->make(SsoJwtExtension::class));
            $twig->addExtension($app->make(WidgetUrlExtension::class));

            return $twig;
        });
    }
}
