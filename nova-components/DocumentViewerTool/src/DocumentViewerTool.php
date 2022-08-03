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

	/**
	 * @param  string  $title
	 *
	 * @return DocumentViewerTool
	 */
    public function withTitle(string $title = null)
    {
		return $this->withMeta([
			'title' => $title ?? null,
		]);
    }

	/**
	 * @param  string  $content
	 *
	 * @return DocumentViewerTool
	 */
	public function withContent(string $content = null)
	{
		return $this->withMeta([
			'content' => $content ?? null,
		]);
	}
}
