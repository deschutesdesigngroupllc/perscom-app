<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Services;

use App\Services\ExpressionLanguageService;
use Tests\Feature\Tenant\TenantTestCase;

class ExpressionLanguageServiceTest extends TenantTestCase
{
    private ExpressionLanguageService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(ExpressionLanguageService::class);
    }

    public function test_it_evaluates_simple_comparison(): void
    {
        $result = $this->service->evaluate('model.status == "active"', [
            'model' => ['status' => 'active'],
        ]);

        $this->assertTrue($result);
    }

    public function test_it_evaluates_comparison_with_false_result(): void
    {
        $result = $this->service->evaluate('model.status == "active"', [
            'model' => ['status' => 'inactive'],
        ]);

        $this->assertFalse($result);
    }

    public function test_it_evaluates_numeric_comparison(): void
    {
        $result = $this->service->evaluate('model.count > 5', [
            'model' => ['count' => 10],
        ]);

        $this->assertTrue($result);
    }

    public function test_it_evaluates_in_array_function(): void
    {
        $result = $this->service->evaluate('in_array(model.role, ["admin", "moderator"])', [
            'model' => ['role' => 'admin'],
        ]);

        $this->assertTrue($result);
    }

    public function test_it_evaluates_in_array_function_with_false_result(): void
    {
        $result = $this->service->evaluate('in_array(model.role, ["admin", "moderator"])', [
            'model' => ['role' => 'user'],
        ]);

        $this->assertFalse($result);
    }

    public function test_it_evaluates_contains_function(): void
    {
        $result = $this->service->evaluate('contains(model.name, "John")', [
            'model' => ['name' => 'John Doe'],
        ]);

        $this->assertTrue($result);
    }

    public function test_it_evaluates_starts_with_function(): void
    {
        $result = $this->service->evaluate('starts_with(model.name, "John")', [
            'model' => ['name' => 'John Doe'],
        ]);

        $this->assertTrue($result);
    }

    public function test_it_evaluates_ends_with_function(): void
    {
        $result = $this->service->evaluate('ends_with(model.name, "Doe")', [
            'model' => ['name' => 'John Doe'],
        ]);

        $this->assertTrue($result);
    }

    public function test_it_evaluates_blank_function(): void
    {
        $result = $this->service->evaluate('blank(model.value)', [
            'model' => ['value' => ''],
        ]);

        $this->assertTrue($result);
    }

    public function test_it_evaluates_filled_function(): void
    {
        $result = $this->service->evaluate('filled(model.value)', [
            'model' => ['value' => 'something'],
        ]);

        $this->assertTrue($result);
    }

    public function test_it_evaluates_changed_function(): void
    {
        $result = $this->service->evaluate('changed("status")', [
            'model' => ['status' => 'active'],
            'original' => ['status' => 'inactive'],
        ]);

        $this->assertTrue($result);
    }

    public function test_it_evaluates_changed_function_with_no_change(): void
    {
        $result = $this->service->evaluate('changed("status")', [
            'model' => ['status' => 'active'],
            'original' => ['status' => 'active'],
        ]);

        $this->assertFalse($result);
    }

    public function test_it_evaluates_old_value_function(): void
    {
        $result = $this->service->evaluate('old_value("status") == "inactive"', [
            'model' => ['status' => 'active'],
            'original' => ['status' => 'inactive'],
        ]);

        $this->assertTrue($result);
    }

    public function test_it_evaluates_new_value_function(): void
    {
        $result = $this->service->evaluate('new_value("status") == "active"', [
            'model' => ['status' => 'active'],
            'original' => ['status' => 'inactive'],
        ]);

        $this->assertTrue($result);
    }

    public function test_it_evaluates_complex_expression(): void
    {
        $result = $this->service->evaluate('model.status == "active" and model.count > 5', [
            'model' => [
                'status' => 'active',
                'count' => 10,
            ],
        ]);

        $this->assertTrue($result);
    }

    public function test_it_evaluates_or_expression(): void
    {
        $result = $this->service->evaluate('model.status == "active" or model.count > 100', [
            'model' => [
                'status' => 'active',
                'count' => 5,
            ],
        ]);

        $this->assertTrue($result);
    }

    public function test_it_evaluates_template_interpolation(): void
    {
        $result = $this->service->evaluateTemplate('Hello {{ model.name }}, your status is {{ model.status }}', [
            'model' => [
                'name' => 'John',
                'status' => 'active',
            ],
        ]);

        $this->assertEquals('Hello John, your status is active', $result);
    }

    public function test_it_evaluates_template_with_nested_values(): void
    {
        $result = $this->service->evaluateTemplate('User {{ model.user.name }} created by {{ model.author.email }}', [
            'model' => [
                'user' => ['name' => 'Alice'],
                'author' => ['email' => 'admin@example.com'],
            ],
        ]);

        $this->assertEquals('User Alice created by admin@example.com', $result);
    }

    public function test_it_handles_null_values_in_templates(): void
    {
        $result = $this->service->evaluateTemplate('Value: {{ model.value }}', [
            'model' => ['value' => null],
        ]);

        $this->assertEquals('Value: ', $result);
    }

    public function test_it_returns_available_functions(): void
    {
        $functions = $this->service->getAvailableFunctions();

        $this->assertArrayHasKey('in_array', $functions);
        $this->assertArrayHasKey('contains', $functions);
        $this->assertArrayHasKey('starts_with', $functions);
        $this->assertArrayHasKey('ends_with', $functions);
        $this->assertArrayHasKey('changed', $functions);
        $this->assertArrayHasKey('old_value', $functions);
        $this->assertArrayHasKey('new_value', $functions);
        $this->assertArrayHasKey('blank', $functions);
        $this->assertArrayHasKey('filled', $functions);
    }
}
