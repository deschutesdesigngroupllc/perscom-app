@props(['location' => null])

@use('App\Services\GeocodeService')

@php
  $coords = GeocodeService::geocode($location);
@endphp

@if ($coords)
  @php
    $lat = $coords['lat'];
    $lon = $coords['lon'];
    $linkUrl = sprintf('https://www.openstreetmap.org/?mlat=%F&mlon=%F#map=16/%F/%F', $lat, $lon, $lat, $lon);
    $mapId = 'event-map-' . substr(md5($location . $lat . $lon), 0, 8);
  @endphp

  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />

  <div class="overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700">
    <div id="{{ $mapId }}" class="h-72 w-full bg-gray-100 dark:bg-gray-900" wire:ignore x-data x-init="const ensureLeaflet = () => new Promise((resolve) => {
        if (window.L) return resolve();
        const s = document.createElement('script');
        s.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
        s.integrity = 'sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=';
        s.crossOrigin = '';
        s.onload = () => resolve();
        document.head.appendChild(s);
    });
    
    ensureLeaflet().then(() => {
        const map = L.map($el, {
            zoomControl: true,
            scrollWheelZoom: false,
            attributionControl: true,
        }).setView([{{ $lat }}, {{ $lon }}], 15);
    
        const lightTiles = L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; <a href=&quot;https://www.openstreetmap.org/copyright&quot;>OpenStreetMap</a> &copy; <a href=&quot;https://carto.com/attributions&quot;>CARTO</a>',
            subdomains: 'abcd',
            maxZoom: 20,
        });
    
        const darkTiles = L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; <a href=&quot;https://www.openstreetmap.org/copyright&quot;>OpenStreetMap</a> &copy; <a href=&quot;https://carto.com/attributions&quot;>CARTO</a>',
            subdomains: 'abcd',
            maxZoom: 20,
        });
    
        const isDark = () => document.documentElement.classList.contains('dark');
        let active = isDark() ? darkTiles : lightTiles;
        active.addTo(map);
    
        const marker = L.marker([{{ $lat }}, {{ $lon }}]).addTo(map);
        marker.bindPopup(@js($coords['display_name']));
    
        const observer = new MutationObserver(() => {
            const next = isDark() ? darkTiles : lightTiles;
            if (next === active) return;
            map.removeLayer(active);
            next.addTo(map);
            active = next;
        });
        observer.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
    
        setTimeout(() => map.invalidateSize(), 50);
    });"></div>
    <div class="flex items-center justify-between bg-gray-50 px-3 py-2 text-xs text-gray-600 dark:bg-gray-800/60 dark:text-gray-400">
      <span class="truncate">{{ $coords['display_name'] }}</span>
      <a href="{{ $linkUrl }}" target="_blank" rel="noopener"
        class="ml-3 shrink-0 font-medium text-primary-600 hover:underline dark:text-primary-400">
        View larger map &rarr;
      </a>
    </div>
  </div>
@endif
