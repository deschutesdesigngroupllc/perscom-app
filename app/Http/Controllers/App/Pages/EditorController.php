<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Pages;

use App\Http\Requests\App\Pages\StorePageRequest;
use App\Models\Page;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

class EditorController
{
    public function index(Page $page): Response
    {
        return Inertia::render('Editor', [
            'page' => $page,
            'content' => $page->content,
        ]);
    }

    public function store(StorePageRequest $request, Page $page): RedirectResponse
    {
        $files = $request->collect('files');

        $page->update([
            'content' => data_get($files->firstWhere('language', 'html'), 'content'),
        ]);

        return to_route('tenant.admin.pages.index', $page)
            ->with('message', 'The page was updated successfully.');
    }
}
