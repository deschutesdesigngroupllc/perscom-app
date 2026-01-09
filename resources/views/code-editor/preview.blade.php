<x-filament-panels::layout.base>
  {!! $html !!}

  <script>
    (function() {
      const originalConsole = {
        log: console.log,
        warn: console.warn,
        error: console.error,
        info: console.info,
        debug: console.debug,
      };

      function sendToParent(level, args) {
        const message = Array.from(args).map(arg => {
          if (arg === null) return 'null';
          if (arg === undefined) return 'undefined';
          if (typeof arg === 'object') {
            try {
              return JSON.stringify(arg, null, 2);
            } catch (e) {
              return String(arg);
            }
          }
          return String(arg);
        }).join(' ');

        window.parent.postMessage({
          type: 'console',
          level: level,
          message: message,
          timestamp: Date.now(),
        }, '*');
      }

      console.log = function(...args) {
        sendToParent('log', args);
        originalConsole.log.apply(console, args);
      };

      console.warn = function(...args) {
        sendToParent('warn', args);
        originalConsole.warn.apply(console, args);
      };

      console.error = function(...args) {
        sendToParent('error', args);
        originalConsole.error.apply(console, args);
      };

      console.info = function(...args) {
        sendToParent('info', args);
        originalConsole.info.apply(console, args);
      };

      console.debug = function(...args) {
        sendToParent('debug', args);
        originalConsole.debug.apply(console, args);
      };

      window.addEventListener('error', function(event) {
        sendToParent('error', [`${event.message} at ${event.filename}:${event.lineno}:${event.colno}`]);
      });

      window.addEventListener('unhandledrejection', function(event) {
        sendToParent('error', [`Unhandled Promise Rejection: ${event.reason}`]);
      });
    })();
  </script>
</x-filament-panels::layout.base>
