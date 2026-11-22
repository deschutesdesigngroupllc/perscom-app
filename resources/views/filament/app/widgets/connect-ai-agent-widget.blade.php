<x-filament-widgets::widget class="fi-connect-ai-agent-widget">
  <div class="relative rounded-xl bg-white p-6 ring-1 ring-primary-600/25 sm:p-8 dark:bg-gray-900 dark:ring-primary-400/25"
    x-data="{
        serverUrl: @js($this->getServerUrl()),
        claudeCommand: @js('claude mcp add --transport http perscom ' . $this->getServerUrl()),
        copied: null,
        copy(text, key) {
            navigator.clipboard.writeText(text);
            this.copied = key;
            setTimeout(() => this.copied = null, 2000)
        }
    }">
    <x-filament::icon-button icon="heroicon-m-x-mark" color="gray" label="Dismiss" tooltip="Dismiss" wire:click="dismiss"
      class="absolute! right-4 top-4" />

    <div class="grid gap-8 lg:grid-cols-5">
      <div class="lg:col-span-3">
        <x-filament::badge color="brass" icon="heroicon-m-sparkles">New</x-filament::badge>

        <h2 class="mt-4 font-[family-name:var(--font-display)] text-2xl font-bold tracking-tight text-gray-950 sm:text-3xl dark:text-white">
          Ask Your AI About Your Personnel
        </h2>

        <p class="mt-2 max-w-2xl text-base text-gray-600 dark:text-gray-400">
          Connect Claude, Cursor or any AI tool that supports MCP. Ask about your roster, records, awards and training in plain
          English. It's read-only, everyone sees only what their account allows, and it doesn't use your PERSCOM AI allowance.
        </p>

        <div class="mt-6">
          <div class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Your MCP Server</div>
          <div
            class="mt-2 flex max-w-xl items-center gap-2 rounded-lg bg-gray-50 p-1.5 pl-3 ring-1 ring-gray-950/10 dark:bg-white/5 dark:ring-white/10">
            <code class="min-w-0 flex-1 truncate font-mono text-sm text-gray-950 dark:text-white">{{ $this->getServerUrl() }}</code>
            <x-filament::button color="gray" size="sm" icon="heroicon-m-clipboard-document" x-on:click="copy(serverUrl, 'url')">
              <span x-text="copied === 'url' ? 'Copied' : 'Copy'">Copy</span>
            </x-filament::button>
          </div>
        </div>

        <div class="mt-6 flex flex-wrap gap-3">
          <x-filament::button tag="a" :href="$this->getSetupUrl()" icon="heroicon-m-bolt">
            Connect Your AI Tool
          </x-filament::button>
          <x-filament::button color="gray" outlined icon="heroicon-m-command-line" x-on:click="copy(claudeCommand, 'claude-code')">
            <span x-text="copied === 'claude-code' ? 'Command Copied' : 'Copy Claude Code Command'">Copy Claude Code Command</span>
          </x-filament::button>
        </div>
      </div>

      <div class="lg:col-span-2">
        <div class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Try Asking</div>
        <ul class="mt-3 space-y-2">
          @foreach ($this->getExamplePrompts() as $index => $prompt)
            <li>
              <button type="button" x-on:click="copy(@js($prompt), 'prompt-{{ $index }}')"
                class="flex w-full items-start gap-3 rounded-lg bg-gray-50 p-3 text-left text-sm text-gray-700 ring-1 ring-gray-950/5 transition hover:bg-gray-100 focus-visible:outline-2 focus-visible:outline-primary-600 dark:bg-white/5 dark:text-gray-300 dark:ring-white/10 dark:hover:bg-white/10">
                <x-filament::icon icon="heroicon-m-chat-bubble-left-right"
                  class="mt-0.5 size-4 shrink-0 text-gray-400 dark:text-gray-500" />
                <span class="flex-1">{{ $prompt }}</span>
                <span class="shrink-0 text-xs text-primary-600 dark:text-primary-400"
                  x-text="copied === 'prompt-{{ $index }}' ? 'Copied' : ''"></span>
              </button>
            </li>
          @endforeach
        </ul>
      </div>
    </div>
  </div>
</x-filament-widgets::widget>
