<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\View;

use Illuminate\Support\Facades\Context;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\Feature\Tenant\TenantTestCase;

class ErrorPagesTest extends TenantTestCase
{
    public function test_every_error_page_is_branded_and_offers_a_way_forward(): void
    {
        Context::add('request_id', 'req-123');

        foreach ([403, 404, 419, 429, 500, 503] as $status) {
            $html = view("errors.{$status}", ['exception' => new HttpException($status)])->render();

            $this->assertStringContainsString("Error {$status}", $html);
            $this->assertStringContainsString('<title>PERSCOM</title>', $html);
            $this->assertStringContainsString('System Status', $html);
            $this->assertStringContainsString('Request ID: req-123', $html);
        }
    }

    public function test_the_default_authorization_message_is_replaced_with_guidance(): void
    {
        $html = view('errors.403', ['exception' => new HttpException(403, 'This action is unauthorized.')])->render();

        $this->assertStringContainsString('Ask an administrator', $html);
        $this->assertStringNotContainsString('This action is unauthorized', $html);
    }

    public function test_a_custom_forbidden_message_is_shown(): void
    {
        $html = view('errors.403', ['exception' => new HttpException(403, 'We are still setting up your organization.')])->render();

        $this->assertStringContainsString('We are still setting up your organization.', $html);
    }

    public function test_an_unknown_route_renders_the_branded_not_found_page(): void
    {
        $this->get('/this-page-does-not-exist-'.uniqid())
            ->assertNotFound()
            ->assertSee('We Couldn&#039;t Find That Page', false);
    }
}
