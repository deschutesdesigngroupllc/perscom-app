<div>
  <x-filament-widgets::widget class="fi-filament-info-widget">
    <x-filament::section>
      <x-slot name="heading">
        Roster
      </x-slot>

      <x-slot name="description">
        This is all the information we hold about the user.
      </x-slot>

      <div class="flex items-center gap-2">
        <x-filament::button color="gray" x-on:click="$dispatch('open-modal', { id: 'roster-widget-preview' })">
          Preview
        </x-filament::button>
      </div>
    </x-filament::section>
  </x-filament-widgets::widget>

  <x-filament::modal id="roster-widget-preview" width="5xl">
    <x-slot name="heading">
      Roster Widget Preview
    </x-slot>

    <x-slot name="description">
      Below is an example of the roster widget.
    </x-slot>

    <div id="perscom_widget_wrapper">
      <script id="perscom_widget" data-apikey="test" data-widget="roster" src="https://widget.perscom.io/widget.js" type="text/javascript">
      </script>
    </div>
  </x-filament::modal>
</div>
