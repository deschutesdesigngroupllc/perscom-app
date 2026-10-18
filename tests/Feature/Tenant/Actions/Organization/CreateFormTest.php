<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Actions\Organization;

use App\Actions\Organization\CreateForm;
use App\Models\Enums\FieldOptionsType;
use App\Models\Enums\FieldType;
use App\Models\Field;
use App\Models\Form;
use Illuminate\Validation\ValidationException;
use Tests\Feature\Tenant\TenantTestCase;

class CreateFormTest extends TenantTestCase
{
    public function test_it_creates_a_form_with_its_fields_in_order(): void
    {
        $form = CreateForm::handle([
            'name' => 'Leave Request',
            'description' => 'Request time off.',
            'success_message' => 'Submitted.',
            'is_public' => true,
            'fields' => [
                ['name' => 'Start Date', 'type' => 'date', 'required' => true],
                ['name' => 'Leave Type', 'type' => 'select', 'options' => ['Annual', 'Sick']],
            ],
        ]);

        $this->assertTrue($form->wasRecentlyCreated);
        $this->assertSame('leave-request', $form->slug);
        $this->assertTrue($form->is_public);

        $this->assertSame(['start_date', 'leave_type'], $form->fields()->pluck('key')->all());

        $startDate = Field::query()->where('key', 'start_date')->firstOrFail();
        $leaveType = Field::query()->where('key', 'leave_type')->firstOrFail();

        $this->assertSame(FieldType::FIELD_DATE, $startDate->type);
        $this->assertTrue($startDate->required);
        $this->assertSame(FieldOptionsType::ARRAY, $leaveType->options_type);
        $this->assertSame(['Annual', 'Sick'], $leaveType->options->getArrayCopy());
    }

    public function test_it_reuses_the_form_and_only_adds_missing_fields(): void
    {
        CreateForm::handle(['name' => 'Leave Request', 'fields' => [['name' => 'Start Date', 'type' => 'date']]]);

        $form = CreateForm::handle([
            'name' => 'leave request',
            'fields' => [
                ['name' => 'Start Date', 'type' => 'date'],
                ['name' => 'End Date', 'type' => 'date'],
            ],
        ]);

        $this->assertFalse($form->wasRecentlyCreated);
        $this->assertSame(1, Form::query()->count());
        $this->assertSame(['start_date', 'end_date'], $form->fields()->pluck('key')->all());
    }

    public function test_it_generates_a_unique_slug(): void
    {
        Form::factory()->createQuietly(['name' => 'Something Else', 'slug' => 'leave-request']);

        $form = CreateForm::handle(['name' => 'Leave Request']);

        $this->assertSame('leave-request-2', $form->slug);
    }

    public function test_it_rejects_an_unknown_field_type(): void
    {
        $this->expectException(ValidationException::class);

        CreateForm::handle(['name' => 'Leave Request', 'fields' => [['name' => 'Start Date', 'type' => 'calendar']]]);
    }
}
