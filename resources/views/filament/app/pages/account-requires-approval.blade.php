@use(Filament\Facades\Filament)

<x-filament-panels::page.simple>
  <p class="text-sm text-gray-500 dark:text-gray-400">
    Our organization requires accounts to be approved before they can login for the first time. Please wait for an administrator to approve
    your account.
  </p>

  <form action="{{ Filament::getLogoutUrl() }}" method="post">
    @csrf

    <p class="text-sm text-gray-500 dark:text-gray-400">
      {{ $this->logoutAction }}
    </p>
  </form>
</x-filament-panels::page.simple>
