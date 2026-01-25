<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Twig;

use App\Models\User;
use App\Support\Twig\Extensions\MathExtension;
use App\Support\Twig\Extensions\SsoJwtExtension;
use App\Support\Twig\Extensions\WidgetUrlExtension;
use Illuminate\Support\Facades\Auth;
use Tests\Feature\Tenant\TenantTestCase;
use Twig\Environment;
use Twig\Extension\SandboxExtension;

class TwigEnvironmentTest extends TenantTestCase
{
    private Environment $twig;

    protected function setUp(): void
    {
        parent::setUp();

        $this->twig = app(Environment::class);
    }

    // -------------------------------------------------------------------------
    // Environment Configuration Tests
    // -------------------------------------------------------------------------

    public function test_twig_environment_is_singleton(): void
    {
        $twig1 = app(Environment::class);
        $twig2 = app(Environment::class);

        $this->assertSame($twig1, $twig2);
    }

    public function test_twig_is_aliased_correctly(): void
    {
        $this->assertInstanceOf(Environment::class, app('twig'));
    }

    public function test_twig_has_sandbox_extension(): void
    {
        $extension = $this->twig->getExtension(SandboxExtension::class);

        $this->assertInstanceOf(SandboxExtension::class, $extension);
    }

    public function test_twig_has_math_extension(): void
    {
        $extension = $this->twig->getExtension(MathExtension::class);

        $this->assertInstanceOf(MathExtension::class, $extension);
    }

    public function test_twig_has_widget_url_extension(): void
    {
        $extension = $this->twig->getExtension(WidgetUrlExtension::class);

        $this->assertInstanceOf(WidgetUrlExtension::class, $extension);
    }

    public function test_twig_has_sso_jwt_extension(): void
    {
        $extension = $this->twig->getExtension(SsoJwtExtension::class);

        $this->assertInstanceOf(SsoJwtExtension::class, $extension);
    }

    public function test_twig_uses_html_autoescape(): void
    {
        $template = $this->twig->createTemplate('{{ value }}');
        $result = $template->render(['value' => '<script>alert("xss")</script>']);

        $this->assertEquals('&lt;script&gt;alert(&quot;xss&quot;)&lt;/script&gt;', $result);
    }

    // -------------------------------------------------------------------------
    // Allowed Tags Tests
    // -------------------------------------------------------------------------

    public function test_if_tag_is_allowed(): void
    {
        $template = $this->twig->createTemplate('{% if show %}visible{% endif %}');
        $result = $template->render(['show' => true]);

        $this->assertEquals('visible', $result);
    }

    public function test_if_else_tag_works(): void
    {
        $template = $this->twig->createTemplate('{% if show %}yes{% else %}no{% endif %}');

        $this->assertEquals('yes', $template->render(['show' => true]));
        $this->assertEquals('no', $template->render(['show' => false]));
    }

    public function test_for_tag_is_allowed(): void
    {
        $template = $this->twig->createTemplate('{% for item in items %}{{ item }}{% endfor %}');
        $result = $template->render(['items' => ['a', 'b', 'c']]);

        $this->assertEquals('abc', $result);
    }

    public function test_for_loop_with_key(): void
    {
        $template = $this->twig->createTemplate('{% for key, value in items %}{{ key }}:{{ value }},{% endfor %}');
        $result = $template->render(['items' => ['a' => 1, 'b' => 2]]);

        $this->assertEquals('a:1,b:2,', $result);
    }

    public function test_set_tag_is_allowed(): void
    {
        $template = $this->twig->createTemplate('{% set greeting = "Hello" %}{{ greeting }} {{ name }}');
        $result = $template->render(['name' => 'World']);

        $this->assertEquals('Hello World', $result);
    }

    public function test_apply_tag_is_allowed(): void
    {
        $template = $this->twig->createTemplate('{% apply upper %}hello{% endapply %}');
        $result = $template->render([]);

        $this->assertEquals('HELLO', $result);
    }

    // -------------------------------------------------------------------------
    // Allowed Filters Tests
    // -------------------------------------------------------------------------

    public function test_capitalize_filter(): void
    {
        $template = $this->twig->createTemplate('{{ value | capitalize }}');
        $result = $template->render(['value' => 'hello world']);

        $this->assertEquals('Hello world', $result);
    }

    public function test_date_filter(): void
    {
        $template = $this->twig->createTemplate('{{ value | date("Y-m-d") }}');
        $result = $template->render(['value' => '2024-01-15 10:30:00']);

        $this->assertEquals('2024-01-15', $result);
    }

    public function test_default_filter(): void
    {
        $template = $this->twig->createTemplate('{{ value | default("fallback") }}');

        $this->assertEquals('fallback', $template->render(['value' => null]));
        $this->assertEquals('actual', $template->render(['value' => 'actual']));
    }

    public function test_escape_filter(): void
    {
        $template = $this->twig->createTemplate('{{ value | escape }}');
        $result = $template->render(['value' => '<b>bold</b>']);

        $this->assertEquals('&lt;b&gt;bold&lt;/b&gt;', $result);
    }

    public function test_first_filter(): void
    {
        $template = $this->twig->createTemplate('{{ items | first }}');
        $result = $template->render(['items' => ['apple', 'banana', 'cherry']]);

        $this->assertEquals('apple', $result);
    }

    public function test_last_filter(): void
    {
        $template = $this->twig->createTemplate('{{ items | last }}');
        $result = $template->render(['items' => ['apple', 'banana', 'cherry']]);

        $this->assertEquals('cherry', $result);
    }

    public function test_join_filter(): void
    {
        $template = $this->twig->createTemplate('{{ items | join(", ") }}');
        $result = $template->render(['items' => ['apple', 'banana', 'cherry']]);

        $this->assertEquals('apple, banana, cherry', $result);
    }

    public function test_json_encode_filter(): void
    {
        $template = $this->twig->createTemplate('{{ value | json_encode | raw }}');
        $result = $template->render(['value' => ['key' => 'value']]);

        $this->assertEquals('{"key":"value"}', $result);
    }

    public function test_length_filter(): void
    {
        $template = $this->twig->createTemplate('{{ items | length }}');
        $result = $template->render(['items' => ['a', 'b', 'c']]);

        $this->assertEquals('3', $result);
    }

    public function test_lower_filter(): void
    {
        $template = $this->twig->createTemplate('{{ value | lower }}');
        $result = $template->render(['value' => 'HELLO']);

        $this->assertEquals('hello', $result);
    }

    public function test_upper_filter(): void
    {
        $template = $this->twig->createTemplate('{{ value | upper }}');
        $result = $template->render(['value' => 'hello']);

        $this->assertEquals('HELLO', $result);
    }

    public function test_title_filter(): void
    {
        $template = $this->twig->createTemplate('{{ value | title }}');
        $result = $template->render(['value' => 'hello world']);

        $this->assertEquals('Hello World', $result);
    }

    public function test_trim_filter(): void
    {
        $template = $this->twig->createTemplate('{{ value | trim }}');
        $result = $template->render(['value' => '  hello  ']);

        $this->assertEquals('hello', $result);
    }

    public function test_nl2br_filter(): void
    {
        $template = $this->twig->createTemplate('{{ value | nl2br }}');
        $result = $template->render(['value' => "line1\nline2"]);

        $this->assertStringContainsString('<br />', $result);
    }

    public function test_replace_filter(): void
    {
        $template = $this->twig->createTemplate('{{ value | replace({"world": "universe"}) }}');
        $result = $template->render(['value' => 'hello world']);

        $this->assertEquals('hello universe', $result);
    }

    public function test_round_filter(): void
    {
        $template = $this->twig->createTemplate('{{ value | round(2) }}');
        $result = $template->render(['value' => 3.14159]);

        $this->assertEquals('3.14', $result);
    }

    public function test_slice_filter(): void
    {
        $template = $this->twig->createTemplate('{{ value | slice(0, 5) }}');
        $result = $template->render(['value' => 'Hello World']);

        $this->assertEquals('Hello', $result);
    }

    public function test_split_filter(): void
    {
        $template = $this->twig->createTemplate('{% for part in value | split(",") %}{{ part }};{% endfor %}');
        $result = $template->render(['value' => 'a,b,c']);

        $this->assertEquals('a;b;c;', $result);
    }

    public function test_striptags_filter(): void
    {
        $template = $this->twig->createTemplate('{{ value | striptags }}');
        $result = $template->render(['value' => '<p>Hello <strong>World</strong></p>']);

        $this->assertEquals('Hello World', $result);
    }

    public function test_raw_filter(): void
    {
        $template = $this->twig->createTemplate('{{ value | raw }}');
        $result = $template->render(['value' => '<b>bold</b>']);

        $this->assertEquals('<b>bold</b>', $result);
    }

    public function test_map_filter(): void
    {
        $template = $this->twig->createTemplate('{{ items | map(v => v * 2) | join(",") }}');
        $result = $template->render(['items' => [1, 2, 3]]);

        $this->assertEquals('2,4,6', $result);
    }

    public function test_reduce_filter(): void
    {
        $template = $this->twig->createTemplate('{{ items | reduce((carry, v) => carry + v, 0) }}');
        $result = $template->render(['items' => [1, 2, 3, 4]]);

        $this->assertEquals('10', $result);
    }

    // -------------------------------------------------------------------------
    // MathExtension Tests
    // -------------------------------------------------------------------------

    public function test_increment_filter(): void
    {
        $template = $this->twig->createTemplate('{{ value | increment }}');
        $result = $template->render(['value' => 5]);

        $this->assertEquals('6', $result);
    }

    public function test_increment_filter_with_custom_amount(): void
    {
        $template = $this->twig->createTemplate('{{ value | increment(10) }}');
        $result = $template->render(['value' => 5]);

        $this->assertEquals('15', $result);
    }

    public function test_increment_filter_with_float(): void
    {
        $template = $this->twig->createTemplate('{{ value | increment(0.5) }}');
        $result = $template->render(['value' => 1.5]);

        $this->assertEquals('2', $result);
    }

    public function test_increment_filter_with_non_numeric_returns_amount(): void
    {
        $template = $this->twig->createTemplate('{{ value | increment(5) }}');
        $result = $template->render(['value' => 'not a number']);

        $this->assertEquals('5', $result);
    }

    public function test_decrement_filter(): void
    {
        $template = $this->twig->createTemplate('{{ value | decrement }}');
        $result = $template->render(['value' => 5]);

        $this->assertEquals('4', $result);
    }

    public function test_decrement_filter_with_custom_amount(): void
    {
        $template = $this->twig->createTemplate('{{ value | decrement(3) }}');
        $result = $template->render(['value' => 10]);

        $this->assertEquals('7', $result);
    }

    public function test_decrement_filter_with_non_numeric_returns_negative_amount(): void
    {
        $template = $this->twig->createTemplate('{{ value | decrement(5) }}');
        $result = $template->render(['value' => 'not a number']);

        $this->assertEquals('-5', $result);
    }

    // -------------------------------------------------------------------------
    // WidgetUrlExtension Tests
    // -------------------------------------------------------------------------

    public function test_widget_url_function(): void
    {
        $expectedUrl = config('app.widget_url');

        $template = $this->twig->createTemplate('{{ widgetUrl() }}');
        $result = $template->render([]);

        $this->assertEquals($expectedUrl, $result);
    }

    // -------------------------------------------------------------------------
    // SsoJwtExtension Tests
    // -------------------------------------------------------------------------

    public function test_sso_jwt_function_generates_token(): void
    {
        $user = User::factory()->createQuietly();
        Auth::guard('web')->login($user);

        $template = $this->twig->createTemplate('{{ ssoJwt() }}');
        $result = $template->render([]);

        $this->assertNotEmpty($result);
        $this->assertMatchesRegularExpression('/^[A-Za-z0-9_-]+\.[A-Za-z0-9_-]+\.[A-Za-z0-9_-]+$/', $result);
    }

    // -------------------------------------------------------------------------
    // Security Policy Tests (Sandbox Configuration)
    // -------------------------------------------------------------------------

    public function test_sandbox_extension_has_security_policy(): void
    {
        $sandbox = $this->twig->getExtension(SandboxExtension::class);

        $this->assertInstanceOf(SandboxExtension::class, $sandbox);
    }

    public function test_sandbox_allowed_tags_are_configured(): void
    {
        // The sandbox policy is configured with specific allowed tags
        // Verify basic allowed tags work
        $template = $this->twig->createTemplate('{% if true %}{% for i in [1] %}{% set x = 1 %}ok{% endfor %}{% endif %}');
        $result = $template->render([]);

        $this->assertEquals('ok', $result);
    }

    public function test_sandbox_allowed_filters_include_custom_extensions(): void
    {
        // Verify custom extension filters are in the allowed list
        $template = $this->twig->createTemplate('{{ 5 | increment | decrement }}');
        $result = $template->render([]);

        $this->assertEquals('5', $result);
    }

    public function test_sandbox_custom_functions_are_available(): void
    {
        // Verify custom extension functions work
        $template = $this->twig->createTemplate('{{ widgetUrl() }}');
        $result = $template->render([]);

        $this->assertEquals(config('app.widget_url'), $result);
    }

    public function test_object_method_access_on_arrays(): void
    {
        // Arrays (not objects) should work fine for dot notation access
        $template = $this->twig->createTemplate('{{ user.name }}');
        $result = $template->render(['user' => ['name' => 'John']]);

        $this->assertEquals('John', $result);
    }

    public function test_filter_chaining_is_safe(): void
    {
        $template = $this->twig->createTemplate('{{ value | trim | upper | lower | capitalize }}');
        $result = $template->render(['value' => '  HELLO WORLD  ']);

        $this->assertEquals('Hello world', $result);
    }

    public function test_template_prevents_xss_by_default(): void
    {
        $template = $this->twig->createTemplate('{{ value }}');
        $result = $template->render(['value' => '<script>alert("xss")</script>']);

        $this->assertStringNotContainsString('<script>', $result);
        $this->assertStringContainsString('&lt;script&gt;', $result);
    }

    public function test_raw_filter_bypasses_escaping(): void
    {
        $template = $this->twig->createTemplate('{{ value | raw }}');
        $result = $template->render(['value' => '<b>bold</b>']);

        $this->assertEquals('<b>bold</b>', $result);
    }

    // -------------------------------------------------------------------------
    // Complex Template Tests
    // -------------------------------------------------------------------------

    public function test_complex_template_with_multiple_features(): void
    {
        $template = $this->twig->createTemplate(<<<'TWIG'
            {% set greeting = "Hello" %}
            {% if user %}
                {{ greeting }} {{ user.name | upper }}!
                {% for role in user.roles %}
                    - {{ role | title }}
                {% endfor %}
            {% endif %}
            TWIG);

        $result = $template->render([
            'user' => [
                'name' => 'john doe',
                'roles' => ['admin', 'moderator'],
            ],
        ]);

        $this->assertStringContainsString('Hello JOHN DOE!', $result);
        $this->assertStringContainsString('Admin', $result);
        $this->assertStringContainsString('Moderator', $result);
    }

    public function test_nested_array_access(): void
    {
        $template = $this->twig->createTemplate('{{ user.profile.address.city }}');
        $result = $template->render([
            'user' => [
                'profile' => [
                    'address' => [
                        'city' => 'New York',
                    ],
                ],
            ],
        ]);

        $this->assertEquals('New York', $result);
    }

    public function test_chained_filters(): void
    {
        $template = $this->twig->createTemplate('{{ value | trim | upper | slice(0, 5) }}');
        $result = $template->render(['value' => '  hello world  ']);

        $this->assertEquals('HELLO', $result);
    }

    public function test_conditional_with_default(): void
    {
        $template = $this->twig->createTemplate('{{ user.nickname | default(user.name) | upper }}');

        $this->assertEquals('JOHN', $template->render([
            'user' => ['name' => 'john', 'nickname' => null],
        ]));
        $this->assertEquals('JD', $template->render([
            'user' => ['name' => 'john', 'nickname' => 'jd'],
        ]));
    }

    public function test_template_with_math_in_loop(): void
    {
        $template = $this->twig->createTemplate(<<<'TWIG'
            {% for item in items %}{{ item.value | increment(loop.index) }},{% endfor %}
            TWIG);

        $result = $template->render([
            'items' => [
                ['value' => 10],
                ['value' => 20],
                ['value' => 30],
            ],
        ]);

        $this->assertEquals('11,22,33,', $result);
    }

    // -------------------------------------------------------------------------
    // Edge Cases and Error Handling Tests
    // -------------------------------------------------------------------------

    public function test_empty_template(): void
    {
        $template = $this->twig->createTemplate('');
        $result = $template->render([]);

        $this->assertEquals('', $result);
    }

    public function test_template_with_only_whitespace(): void
    {
        $template = $this->twig->createTemplate('   ');
        $result = $template->render([]);

        $this->assertEquals('   ', $result);
    }

    public function test_undefined_variable_returns_empty(): void
    {
        $template = $this->twig->createTemplate('{{ undefined_var | default("") }}');
        $result = $template->render([]);

        $this->assertEquals('', $result);
    }

    public function test_null_value_handling(): void
    {
        $template = $this->twig->createTemplate('{{ value | default("N/A") }}');
        $result = $template->render(['value' => null]);

        $this->assertEquals('N/A', $result);
    }

    public function test_empty_array_in_for_loop(): void
    {
        $template = $this->twig->createTemplate('{% for item in items %}{{ item }}{% else %}empty{% endfor %}');
        $result = $template->render(['items' => []]);

        $this->assertEquals('empty', $result);
    }

    public function test_boolean_in_condition(): void
    {
        $template = $this->twig->createTemplate('{% if active %}yes{% else %}no{% endif %}');

        $this->assertEquals('yes', $template->render(['active' => true]));
        $this->assertEquals('no', $template->render(['active' => false]));
    }

    public function test_special_characters_in_output(): void
    {
        $template = $this->twig->createTemplate('{{ value }}');
        $result = $template->render(['value' => '& < > " \'']);

        $this->assertStringContainsString('&amp;', $result);
        $this->assertStringContainsString('&lt;', $result);
        $this->assertStringContainsString('&gt;', $result);
    }

    public function test_unicode_handling(): void
    {
        $template = $this->twig->createTemplate('{{ value | upper }}');
        $result = $template->render(['value' => 'héllo wörld']);

        $this->assertEquals('HÉLLO WÖRLD', $result);
    }
}
