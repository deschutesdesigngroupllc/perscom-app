<?php

namespace App\Traits;

use Exception;

trait HasEventPrompts
{
    /**
     * @throws Exception
     */
    public function generatePromptForEvent($event, string $type = 'headline'): mixed
    {
        if (! static::$prompts) {
            throw new Exception('The $prompts property has not been set');
        }

        $instance = new static::$prompts;

        if (method_exists($instance, $event)) {
            return call_user_func([$instance, $event], $this, $type);
        }

        return null;
    }
}
