<?php

namespace Perscom\ResourceCustomField;

use Laravel\Nova\ResourceTool;

class ResourceCustomField extends ResourceTool
{
    /**
     * Get the displayable name of the resource tool.
     *
     * @return string
     */
    public function name()
    {
        return 'Resource Custom Field';
    }

    /**
     * Get the component name for the resource tool.
     *
     * @return string
     */
    public function component()
    {
        return 'resource-custom-field';
    }
}
