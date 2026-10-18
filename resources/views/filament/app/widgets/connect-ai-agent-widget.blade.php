<x-filament-widgets::widget class="fi-connect-ai-agent-widget">
  <div
    class="relative overflow-hidden rounded-xl bg-linear-to-br from-primary-600 via-primary-700 to-gray-900 p-6 text-white shadow-sm ring-1 ring-gray-950/5 sm:p-8 dark:ring-white/10"
    x-data="{
        copied: null,
        copy(text, key) {
            navigator.clipboard.writeText(text);
            this.copied = key;
            setTimeout(() => this.copied = null, 2000)
        }
    }">
    <div class="pointer-events-none absolute -right-24 -top-24 size-72 rounded-full bg-white/10 blur-3xl"></div>

    <x-filament::icon-button icon="heroicon-m-x-mark" color="gray" label="Dismiss" tooltip="Dismiss" wire:click="dismiss"
      class="absolute! right-4 top-4 text-white/70! hover:bg-white/10 hover:text-white!" />

    <div class="relative grid gap-8 lg:grid-cols-5">
      <div class="lg:col-span-3">
        <div class="inline-flex items-center gap-1.5 rounded-full bg-white/15 px-3 py-1 text-xs font-semibold uppercase tracking-wide">
          <x-filament::icon icon="heroicon-m-sparkles" class="size-4" />
          New
        </div>

        <h2 class="mt-4 text-2xl font-bold tracking-tight sm:text-3xl">
          Ask your AI about your personnel
        </h2>

        <p class="mt-2 max-w-2xl text-base text-white/80">
          Connect Claude, Cursor or any other AI tool that supports MCP to PERSCOM. Ask about your roster, records,
          awards and training in plain English. It is read-only, every person sees only what their account allows, and it
          does not use your PERSCOM AI allowance.
        </p>

        <div class="mt-6">
          <div class="text-xs font-semibold uppercase tracking-wide text-white/70">Your MCP server</div>
          <div class="mt-2 flex max-w-xl items-center gap-2 rounded-lg bg-gray-950/30 p-1.5 pl-3 ring-1 ring-white/15">
            <code class="min-w-0 flex-1 truncate font-mono text-sm">{{ $this->getServerUrl() }}</code>
            <button type="button" x-on:click="copy(@js($this->getServerUrl()), 'url')"
              class="inline-flex shrink-0 items-center gap-1.5 rounded-md bg-white px-3 py-1.5 text-sm font-semibold text-gray-900 transition hover:bg-white/90">
              <x-filament::icon icon="heroicon-m-clipboard-document" class="size-4" />
              <span x-text="copied === 'url' ? 'Copied' : 'Copy'">Copy</span>
            </button>
          </div>
        </div>

        <div class="mt-6 flex flex-wrap gap-3">
          <a href="{{ $this->getSetupUrl() }}"
            class="inline-flex items-center gap-2 rounded-lg bg-white px-4 py-2.5 text-sm font-semibold text-primary-700 shadow-sm transition hover:bg-primary-50 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white">
            <x-filament::icon icon="heroicon-m-bolt" class="size-5 text-primary-600" />
            Connect Your AI Tool
          </a>
          <button type="button" x-on:click="copy(@js('claude mcp add --transport http perscom ' . $this->getServerUrl()), 'claude-code')"
            class="inline-flex items-center gap-1.5 rounded-lg px-3 py-2 text-sm font-semibold text-white ring-1 ring-white/30 transition hover:bg-white/10">
            <x-filament::icon icon="heroicon-m-command-line" class="size-4" />
            <span x-text="copied === 'claude-code' ? 'Command Copied' : 'Copy Claude Code Command'">Copy Claude Code Command</span>
          </button>
        </div>
      </div>

      <div class="lg:col-span-2">
        <div class="text-xs font-semibold uppercase tracking-wide text-white/70">Try asking</div>
        <ul class="mt-3 space-y-2">
          @foreach ($this->getExamplePrompts() as $index => $prompt)
            <li>
              <button type="button" x-on:click="copy(@js($prompt), 'prompt-{{ $index }}')"
                class="group flex w-full items-start gap-3 rounded-lg bg-white/10 p-3 text-left text-sm ring-1 ring-white/10 transition hover:bg-white/15">
                <x-filament::icon icon="heroicon-m-chat-bubble-left-right" class="mt-0.5 size-4 shrink-0 text-white/70" />
                <span class="flex-1">{{ $prompt }}</span>
                <span class="shrink-0 text-xs text-white/60" x-text="copied === 'prompt-{{ $index }}' ? 'Copied' : ''"></span>
              </button>
            </li>
          @endforeach
        </ul>
      </div>
    </div>
  </div>
</x-filament-widgets::widget>
