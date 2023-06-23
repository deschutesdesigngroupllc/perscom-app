<?php

namespace App\Traits;

use Exception;

trait HasEventPrompts
{
    /**
     * @return mixed|null
     *
     * @throws Exception
     */
    public function generatePromptForEvent($event, string $type = 'headline')
    {
        if (! $this->prompts) {
            throw new Exception('The $prompts property has not been set');
        }

        $instance = new $this->prompts;

        if (method_exists($instance, $event)) {
            return call_user_func([$instance, $event], $this, $type);
        }

        return null;
    }
}
