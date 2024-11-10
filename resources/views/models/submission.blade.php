<style>
  .grid {
    display: grid;
  }

  .gap-y-2 {
    row-gap: 0.5rem;
  }

  .space-y-6> :not([hidden])~ :not([hidden]) {
    --tw-space-y-reverse: 0;
    margin-top: calc(1.5rem * calc(1 - var(--tw-space-y-reverse)));
    margin-bottom: calc(1.5rem * var(--tw-space-y-reverse));
  }

  .text-sm {
    font-size: 0.875rem;
    line-height: 1.25rem;
  }

  .font-semibold {
    font-weight: 600;
  }

  .leading-6 {
    line-height: 1.5rem;
  }
</style>

@php
  $submission = $submission ?? $getRecord();
@endphp

<div class="space-y-6">
  @forelse($submission->form->fields as $field)
    <dl class="grid gap-y-2">
      <dt class="text-sm font-semibold leading-6">{{ $field->name }}</dt>
      <dd class="text-sm leading-6">{{ data_get($submission, $field->key, 'No Value') }}</dd>
    </dl>
  @empty
    <div>There are no elements for this form.</div>
  @endforelse
</div>
