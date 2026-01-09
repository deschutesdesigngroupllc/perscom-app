<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Pages;

use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Loader\ArrayLoader;

class PreviewController
{
    protected Environment $twig;

    /**
     * @throws BindingResolutionException
     */
    public function __construct(
        protected readonly Container $app,
        protected readonly Repository $config,
    ) {
        $this->twig = $this->app->make('twig');
        $this->twig->setLoader(new ArrayLoader([
            'template' => '',
        ]));

        $this->config->set('debugbar.enabled', false);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function __invoke(Request $request): Response
    {
        /** @var ArrayLoader $loader */
        $loader = $this->twig->getLoader();
        $loader->setTemplate('html', $request->input('html', ''));

        return response()->view('code-editor.preview', [
            'html' => $this->twig->render('html'),
        ]);
    }
}
