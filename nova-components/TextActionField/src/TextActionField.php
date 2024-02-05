<?php

namespace Perscom\TextActionField;

use Closure;
use Laravel\Nova\Fields\AsHTML;
use Laravel\Nova\Fields\Copyable;
use Laravel\Nova\Fields\Field;
use Laravel\SerializableClosure\Exceptions\PhpVersionNotSupportedException;
use Laravel\SerializableClosure\SerializableClosure;

class TextActionField extends Field
{
    use AsHTML;
    use Copyable;

    public $component = 'text-action-field';

    public string $actionText;

    public string $actionMessage;

    public Closure $actionCallback;

    public function actionText(string $text): static
    {
        $this->actionText = $text;

        return $this;
    }

    public function getActionText(): string
    {
        return $this->actionText;
    }

    public function actionMessage(string $text): static
    {
        $this->actionMessage = $text;

        return $this;
    }

    public function getActionMessage(): string
    {
        return $this->actionMessage;
    }

    public function actionCallback(Closure $callback): static
    {
        $this->actionCallback = $callback;

        return $this;
    }

    public function getActionCallback(): Closure
    {
        return $this->actionCallback;
    }

    /**
     * @return array<string, mixed>
     *
     * @throws PhpVersionNotSupportedException
     */
    public function jsonSerialize(): array
    {
        return array_merge(parent::jsonSerialize(), [
            'asHtml' => $this->asHtml,
            'copyable' => $this->copyable,
            'actionText' => $this->getActionText(),
            'actionMessage' => $this->getActionMessage(),
            'actionCallback' => serialize(new SerializableClosure($this->getActionCallback())),
        ]);
    }
}
