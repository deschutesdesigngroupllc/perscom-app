<x-filament-panels::page.simple>
  <p class="text-center text-sm text-gray-500 dark:text-gray-400">
    Our organization requires accounts to be approved before they can login for the first time. Please wait for an administrator to approve
    your account.
  </p>

  <form action="{{ route('filament.app.auth.logout') }}" method="post">
    @csrf

    <p class="text-center text-sm text-gray-500 dark:text-gray-400">
      {{ $this->logoutAction }}
    </p>
  </form>
</x-filament-panels::page.simple>
