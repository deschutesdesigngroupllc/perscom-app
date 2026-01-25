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
        $result = $this->service->evaluate('model["status"] == "active"', [
            'model' => ['status' => 'active'],
        ]);

        $this->assertTrue($result);
    }

    public function test_it_evaluates_comparison_with_false_result(): void
    {
        $result = $this->service->evaluate('model["status"] == "active"', [
            'model' => ['status' => 'inactive'],
        ]);

        $this->assertFalse($result);
    }

    public function test_it_evaluates_numeric_comparison(): void
    {
        $result = $this->service->evaluate('model["count"] > 5', [
            'model' => ['count' => 10],
        ]);

        $this->assertTrue($result);
    }

    public function test_it_evaluates_in_array_function(): void
    {
        $result = $this->service->evaluate('in_array(model["role"], ["admin", "moderator"])', [
            'model' => ['role' => 'admin'],
        ]);

        $this->assertTrue($result);
    }

    public function test_it_evaluates_in_array_function_with_false_result(): void
    {
        $result = $this->service->evaluate('in_array(model["role"], ["admin", "moderator"])', [
            'model' => ['role' => 'user'],
        ]);

        $this->assertFalse($result);
    }

    public function test_it_evaluates_contains_operator(): void
    {
        $result = $this->service->evaluate('model["name"] contains "John"', [
            'model' => ['name' => 'John Doe'],
        ]);

        $this->assertTrue($result);
    }

    public function test_it_evaluates_starts_with_function(): void
    {
        $result = $this->service->evaluate('starts_with(model["name"], "John")', [
            'model' => ['name' => 'John Doe'],
        ]);

        $this->assertTrue($result);
    }

    public function test_it_evaluates_ends_with_function(): void
    {
        $result = $this->service->evaluate('ends_with(model["name"], "Doe")', [
            'model' => ['name' => 'John Doe'],
        ]);

        $this->assertTrue($result);
    }

    public function test_it_evaluates_blank_function(): void
    {
        $result = $this->service->evaluate('blank(model["value"])', [
            'model' => ['value' => ''],
        ]);

        $this->assertTrue($result);
    }

    public function test_it_evaluates_filled_function(): void
    {
        $result = $this->service->evaluate('filled(model["value"])', [
            'model' => ['value' => 'something'],
        ]);

        $this->assertTrue($result);
    }

    public function test_it_evaluates_changed_function(): void
    {
        $result = $this->service->evaluate('changed("status")', [
            'model' => ['status' => 'active'],
            'changes' => ['status' => ['old' => 'inactive', 'new' => 'active']],
        ]);

        $this->assertTrue($result);
    }

    public function test_it_evaluates_changed_function_with_no_change(): void
    {
        $result = $this->service->evaluate('changed("status")', [
            'model' => ['status' => 'active'],
            'changes' => [],
        ]);

        $this->assertFalse($result);
    }

    public function test_it_evaluates_old_value_function(): void
    {
        $result = $this->service->evaluate('old_value("status") == "inactive"', [
            'model' => ['status' => 'active'],
            'changes' => ['status' => ['old' => 'inactive', 'new' => 'active']],
        ]);

        $this->assertTrue($result);
    }

    public function test_it_evaluates_new_value_function(): void
    {
        $result = $this->service->evaluate('new_value("status") == "active"', [
            'model' => ['status' => 'active'],
            'changes' => ['status' => ['old' => 'inactive', 'new' => 'active']],
        ]);

        $this->assertTrue($result);
    }

    public function test_it_evaluates_complex_expression(): void
    {
        $result = $this->service->evaluate('model["status"] == "active" and model["count"] > 5', [
            'model' => [
                'status' => 'active',
                'count' => 10,
            ],
        ]);

        $this->assertTrue($result);
    }

    public function test_it_evaluates_or_expression(): void
    {
        $result = $this->service->evaluate('model["status"] == "active" or model["count"] > 100', [
            'model' => [
                'status' => 'active',
                'count' => 5,
            ],
        ]);

        $this->assertTrue($result);
    }

    public function test_it_evaluates_string_concatenation(): void
    {
        $result = $this->service->evaluate('"Hello " ~ model["name"] ~ ", your status is " ~ model["status"]', [
            'model' => [
                'name' => 'John',
                'status' => 'active',
            ],
        ]);

        $this->assertEquals('Hello John, your status is active', $result);
    }

    public function test_it_evaluates_nested_array_access(): void
    {
        $result = $this->service->evaluate('model["user"]["name"] == "Alice" and model["author"]["email"] == "admin@example.com"', [
            'model' => [
                'user' => ['name' => 'Alice'],
                'author' => ['email' => 'admin@example.com'],
            ],
        ]);

        $this->assertTrue($result);
    }

    public function test_it_handles_null_values(): void
    {
        $result = $this->service->evaluate('blank(model["value"])', [
            'model' => ['value' => null],
        ]);

        $this->assertTrue($result);
    }

    public function test_it_returns_available_functions(): void
    {
        $functions = ExpressionLanguageService::getAvailableFunctions();

        $this->assertArrayHasKey('in_array(needle, haystack)', $functions);
        $this->assertArrayHasKey('contains(haystack, needle)', $functions);
        $this->assertArrayHasKey('starts_with(haystack, needle)', $functions);
        $this->assertArrayHasKey('ends_with(haystack, needle)', $functions);
        $this->assertArrayHasKey('changed(key)', $functions);
        $this->assertArrayHasKey('old_value(key)', $functions);
        $this->assertArrayHasKey('new_value(key)', $functions);
        $this->assertArrayHasKey('blank(value)', $functions);
        $this->assertArrayHasKey('filled(value)', $functions);
    }
}
