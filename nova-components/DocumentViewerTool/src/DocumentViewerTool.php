<?php

namespace Perscom\DocumentViewerTool;

use App\Models\Document;
use Laravel\Nova\ResourceTool;

class DocumentViewerTool extends ResourceTool
{
    /**
     * Get the displayable name of the resource tool.
     *
     * @return string
     */
    public function name()
    {
        return 'Document Viewer Tool';
    }

    /**
     * Get the component name for the resource tool.
     *
     * @return string
     */
    public function component()
    {
        return 'document-viewer-tool';
    }

    public function withDocument(?Document $document)
    {
		return $this->withMeta([
			'title' => $document->name ?? null,
			'content' => $document->content ?? null
		]);
    }
}
