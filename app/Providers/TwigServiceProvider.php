<?php

declare(strict_types=1);

namespace App\Providers;

use App\Support\Twig\Extensions\MathExtension;
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
        $this->app->singleton(Environment::class, function (Container $app): Environment {
            $loader = new FilesystemLoader(resource_path('views/twig'));
            $policy = new SecurityPolicy(
                allowedTags: ['apply', 'if', 'for', 'set'],
                allowedFilters: ['capitalize', 'date', 'decrement', 'default', 'escape', 'find', 'first', 'format_currency', 'format_date', 'format_number', 'format_time', 'increment', 'join', 'json_encode', 'last', 'length', 'lower', 'map', 'nl2br', 'plural', 'raw', 'reduce', 'replace', 'round', 'singular', 'slice', 'split', 'striptags', 'title', 'trim', 'upper'],
                allowedMethods: [],
                allowedProperties: [],
                allowedFunctions: []
            );

            $twig = new Environment($loader, [
                'cache' => storage_path('framework/twig'),
                'debug' => config('app.debug'),
                'autoescape' => 'html',
            ]);

            $twig->addExtension(new SandboxExtension($policy));
            $twig->addExtension($app->make(MathExtension::class));
            $twig->addExtension($app->make(SsoJwtExtension::class));
            $twig->addExtension($app->make(WidgetUrlExtension::class));

            return $twig;
        });

        $this->app->alias(Environment::class, 'twig');
    }
}
