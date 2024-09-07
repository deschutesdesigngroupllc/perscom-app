<div class="inline-block min-w-full align-middle">
  <table class="min-w-full divide-y divide-gray-300 dark:divide-gray-700">
    <thead>
      <tr>
        <th scope="col" class="py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white sm:pl-0">Tag</th>
        <th scope="col" class="hidden py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white lg:table-cell">Description</th>
      </tr>
    </thead>
    <tbody class="divide-y divide-gray-200 dark:divide-gray-700" x-init="() => {
        Alpine.magic('clipboard', () => subject => {
            navigator.clipboard.writeText(subject)
        })
    }">
      @foreach ($tags as $tag => $description)
        <tr>
          <td class="whitespace-nowrap py-4 text-sm font-medium text-gray-900 dark:text-white">
            <div x-on:click="() => {
                $clipboard('{{ $tag }}')
                close()
            }"
              class="cursor-pointer">
              {{ $tag }}
            </div>
            <dl class="font-normal lg:hidden">
              <dd class="mt-1 truncate text-gray-500 text-wrap">{{ $description }}</dd>
            </dl>
          </td>
          <td class="hidden py-4 text-sm text-gray-500 lg:table-cell">{{ $description }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>
