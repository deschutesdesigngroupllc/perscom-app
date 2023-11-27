<div class="hf-flex hf-flex-col">
    <div class="hf-overflow-x-auto">
        <div class="hf-inline-block hf-min-w-full hf-align-middle">
            <div class="hf-overflow-hidden hf-rounded-lg">
                <table class="hf-min-w-full hf-divide-y hf-divide-gray-100 dark:hf-divide-gray-700">
                    <thead class="hf-bg-gray-50 dark:hf-bg-gray-700">
                    <tr>
                        <th scope="col"
                            class="hf-py-3.5 hf-pl-4 hf-pr-3 hf-text-left hf-text-sm hf-font-semibold hf-text-gray-500 dark:hf-text-gray-400 sm:hf-pl-6">
                            Tag
                        </th>
                        <th scope="col"
                            class="hf-px-3 hf-py-3.5 hf-text-left hf-text-sm hf-font-semibold hf-text-gray-500 dark:hf-text-gray-400">Value
                        </th>
                    </tr>
                    </thead>
                    <tbody class="hf-divide-y hf-divide-gray-100 dark:hf-divide-gray-700 hf-bg-white dark:hf-bg-gray-800">
                    @foreach(\App\Models\Document::$availableTags as $tag => $description)
                        <tr>
                            <td class="hf-whitespace-nowrap hf-py-4 hf-pl-4 hf-pr-3 hf-text-sm hf-text-gray-500 dark:hf-text-gray-400 sm:hf-pl-6 hf-lead">{{ $tag }}</td>
                            <td class="hf-whitespace-nowrap hf-px-3 hf-py-4 hf-text-sm hf-text-gray-500 dark:hf-text-gray-400">{{ $description }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>