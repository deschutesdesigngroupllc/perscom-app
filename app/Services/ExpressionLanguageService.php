<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\ExpressionEvaluationException;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Throwable;

class ExpressionLanguageService
{
    protected ExpressionLanguage $expressionLanguage;

    public function __construct()
    {
        $this->expressionLanguage = new ExpressionLanguage;
        $this->registerCustomFunctions();
    }

    /**
     * Get list of available functions for documentation.
     *
     * @return array<string, string>
     */
    public static function getAvailableFunctions(): array
    {
        return [
            'in_array(needle, haystack)' => 'Check if value exists in array',
            'contains(haystack, needle)' => 'Check if string contains substring',
            'starts_with(haystack, needle)' => 'Check if string starts with prefix',
            'ends_with(haystack, needle)' => 'Check if string ends with suffix',
            'changed(key)' => 'Check if attribute was changed (requires changes context)',
            'strip_characters(text)' => 'Check if attribute was changed (requires changes context)',
            'old_value(key)' => 'Get old value of changed attribute',
            'new_value(key)' => 'Get new value of changed attribute',
            'blank(value)' => 'Check if value is empty',
            'filled(value)' => 'Check if value is not empty',
        ];
    }

    /**
     * Get list of available context variables.
     *
     * @return array<string, string>
     */
    public static function getAvailableContextVariables(): array
    {
        return [
            'model' => 'The subject model data as an array',
            'model_type' => 'The fully qualified class name of the model',
            'model_id' => 'The primary key of the model',
            'changes' => 'Array of changed attributes with old and new values',
            'causer' => 'The user who triggered the event (if available)',
            'causer_id' => 'The ID of the user who triggered the event',
            'now' => 'The current datetime',
        ];
    }

    /**
     * Evaluate an expression with the given context.
     *
     * @param  array<string, mixed>  $context
     *
     * @throws ExpressionEvaluationException
     */
    public function evaluate(string $expression, array $context = []): mixed
    {
        try {
            return $this->expressionLanguage->evaluate($expression, $context);
        } catch (Throwable $throwable) {
            throw new ExpressionEvaluationException($expression, $throwable->getMessage(), $throwable);
        }
    }

    /**
     * Validate an expression without evaluating it.
     *
     * @param  array<string>  $allowedVariables
     */
    public function validate(string $expression, array $allowedVariables = []): bool
    {
        try {
            // Use default variable names if not provided
            if ($allowedVariables === []) {
                $allowedVariables = array_keys(self::getAvailableContextVariables());
            }

            $this->expressionLanguage->parse($expression, $allowedVariables);

            return true;
        } catch (Throwable) {
            return false;
        }
    }

    /**
     * Get validation error message for an expression.
     *
     * @param  array<string>  $allowedVariables
     */
    public function getValidationError(string $expression, array $allowedVariables = []): ?string
    {
        try {
            // Use default variable names if not provided
            if ($allowedVariables === []) {
                $allowedVariables = array_keys(self::getAvailableContextVariables());
            }

            $this->expressionLanguage->parse($expression, $allowedVariables);

            return null;
        } catch (Throwable $throwable) {
            return $throwable->getMessage();
        }
    }

    /**
     * Register custom functions for the expression language.
     */
    protected function registerCustomFunctions(): void
    {
        $this->expressionLanguage->register(
            'in_array',
            fn (string $needle, $haystack): string => sprintf('in_array(%s, %s)', $needle, $haystack),
            fn ($arguments, $needle, $haystack): bool => is_array($haystack) && in_array($needle, $haystack, false)
        );

        $this->expressionLanguage->register(
            'contains',
            fn (string $haystack, $needle): string => sprintf('str_contains(%s, %s)', $haystack, $needle),
            fn ($arguments, $haystack, $needle): bool => is_string($haystack) && is_string($needle) && str_contains($haystack, $needle)
        );

        $this->expressionLanguage->register(
            'starts_with',
            fn (string $haystack, $needle): string => sprintf('str_starts_with(%s, %s)', $haystack, $needle),
            fn ($arguments, $haystack, $needle): bool => is_string($haystack) && is_string($needle) && str_starts_with($haystack, $needle)
        );

        $this->expressionLanguage->register(
            'ends_with',
            fn (string $haystack, $needle): string => sprintf('str_ends_with(%s, %s)', $haystack, $needle),
            fn ($arguments, $haystack, $needle): bool => is_string($haystack) && is_string($needle) && str_ends_with($haystack, $needle)
        );

        $this->expressionLanguage->register(
            'changed',
            fn (string $key): string => sprintf('isset($changes[%s])', $key),
            function (array $arguments, $key): bool {
                $changes = $arguments['changes'] ?? null;

                return is_array($changes) && array_key_exists($key, $changes);
            }
        );

        $this->expressionLanguage->register(
            'old_value',
            fn (string $key): string => sprintf('$changes[%s]["old"] ?? null', $key),
            function (array $arguments, $key) {
                $changes = $arguments['changes'] ?? null;

                return $changes[$key]['old'] ?? null;
            }
        );

        $this->expressionLanguage->register(
            'new_value',
            fn (string $key): string => sprintf('$changes[%s]["new"] ?? null', $key),
            function (array $arguments, $key) {
                $changes = $arguments['changes'] ?? null;

                return $changes[$key]['new'] ?? null;
            }
        );

        $this->expressionLanguage->register(
            'blank',
            fn (string $value): string => sprintf('blank(%s)', $value),
            fn ($arguments, $value): bool => blank($value)
        );

        $this->expressionLanguage->register(
            'filled',
            fn (string $value): string => sprintf('filled(%s)', $value),
            fn ($arguments, $value): bool => filled($value)
        );
    }
}
