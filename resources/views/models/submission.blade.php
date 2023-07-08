<style>
    .m-0 {
        margin: 0px;
    }

    .mt-1 {
        margin-top: 0.25rem;
    }

    .mt-4 {
        margin-top: 1rem;
    }

    .divide-y > :not([hidden]) ~ :not([hidden]) {
        --tw-divide-y-reverse: 0;
        border-top-width: calc(1px * calc(1 - var(--tw-divide-y-reverse)));
        border-bottom-width: calc(1px * var(--tw-divide-y-reverse));
    }

    .divide-gray-100 > :not([hidden]) ~ :not([hidden]) {
        --tw-divide-opacity: 1;
        border-color: rgb(243 244 246 / var(--tw-divide-opacity));
    }

    .bg-gray-50 {
        --tw-bg-opacity: 1;
        background-color: rgb(249 250 251 / var(--tw-bg-opacity));
    }

    .p-2 {
        padding: 0.5rem;
    }

    .text-sm {
        font-size: 0.875rem;
        line-height: 1.25rem;
    }

    .font-bold {
        font-weight: 700;
    }

    .font-semibold {
        font-weight: 600;
    }

    .leading-6 {
        line-height: 1.5rem;
    }

    .leading-7 {
        line-height: 1.75rem;
    }

    @media (min-width: 640px) {
        .sm\:col-span-2 {
            grid-column: span 2 / span 2;
        }

        .sm\:mt-0 {
            margin-top: 0px;
        }

        .sm\:grid {
            display: grid;
        }

        .sm\:grid-cols-3 {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .sm\:gap-2 {
            gap: 0.5rem;
        }
    }
</style>

<div class="bg-gray-50 p-2">
    @forelse($submission->form->fields as $field)
        <dl class="divide-y divide-gray-100 m-0">
            <div class="sm:grid sm:grid-cols-3 sm:gap-2">
                <dt class="text-sm font-semibold leading-6">{{ $field->name }}</dt>
                <dd class="mt-1 text-sm leading-6 sm:col-span-2 sm:mt-0">{{ $field->getHumanReadableFormat($submission->getAttribute($field->key)) }}</dd>
            </div>
        </dl>
    @empty
        <div>There are no elements for this form.</div>
    @endforelse
</div>