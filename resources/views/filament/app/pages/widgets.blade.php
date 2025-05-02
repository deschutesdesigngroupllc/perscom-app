<x-filament-panels::page>
  <div id="perscom_widget_wrapper">
    <script id="perscom_widget" data-apikey="{{ $this->apiKey }}" data-widget={{ $this->widget }} src="{{ config('app.widget_url') }}"
      type="text/javascript"></script>
  </div>
</x-filament-panels::page>
