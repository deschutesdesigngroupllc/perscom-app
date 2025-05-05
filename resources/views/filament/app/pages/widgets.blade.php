<x-filament-panels::page>
  <div x-data="{
    init() {
      const script = document.createElement('script');
      script.id = 'perscom_widget';
      script.src = '{{ config('app.widget_url') }}';
      script.type = 'text/javascript';
      script.setAttribute('data-apikey', '{{ $this->apiKey }}');
      script.setAttribute('data-widget', '{{ $this->widget }}');

      if (document.documentElement.classList.contains('dark')) {
        script.setAttribute('data-dark', 'true');
      }

      document.getElementById('perscom_widget_wrapper')?.appendChild(script);
    }
  }" x-init="init">
    <div id="perscom_widget_wrapper"></div>
  </div>
</x-filament-panels::page>
