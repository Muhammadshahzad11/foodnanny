@php
    // Always same-origin relative URLs. Absolute APP_URL (e.g. :8002) breaks install when browsing :8000.
    $pwaIcon192 = '/images/default/pwa/icons/icon-192x192.png';
    $pwaIcon512 = '/images/default/pwa/icons/icon-512x512.png';
@endphp
<!-- Web Application Manifest (relative = same origin as current port) -->
<link rel="manifest" href="/manifest.json">
<!-- Chrome for Android theme color -->
<meta name="theme-color" content="{{ $config['theme_color'] }}">

<!-- Add to homescreen for Chrome on Android -->
<meta name="mobile-web-app-capable" content="{{ $config['display'] == 'standalone' ? 'yes' : 'no' }}">
<meta name="application-name" content="{{ $config['short_name'] }}">
<link rel="icon" sizes="512x512" href="{{ $pwaIcon512 }}">

<!-- Add to homescreen for Safari on iOS -->
<meta name="apple-mobile-web-app-capable" content="{{ $config['display'] == 'standalone' ? 'yes' : 'no' }}">
<meta name="apple-mobile-web-app-status-bar-style" content="{{  $config['status_bar'] }}">
<meta name="apple-mobile-web-app-title" content="{{ $config['short_name'] }}">
<link rel="apple-touch-icon" href="{{ $pwaIcon512 }}">
<link rel="apple-touch-icon" sizes="192x192" href="{{ $pwaIcon192 }}">

<link href="/images/default/pwa/splashes/splash-640x1136.png"
      media="(device-width: 320px) and (device-height: 568px) and (-webkit-device-pixel-ratio: 2)"
      rel="apple-touch-startup-image"/>
<link href="/images/default/pwa/splashes/splash-750x1334.png"
      media="(device-width: 375px) and (device-height: 667px) and (-webkit-device-pixel-ratio: 2)"
      rel="apple-touch-startup-image"/>
<link href="/images/default/pwa/splashes/splash-1242x2208.png"
      media="(device-width: 621px) and (device-height: 1104px) and (-webkit-device-pixel-ratio: 3)"
      rel="apple-touch-startup-image"/>
<link href="/images/default/pwa/splashes/splash-1125x2436.png"
      media="(device-width: 375px) and (device-height: 812px) and (-webkit-device-pixel-ratio: 3)"
      rel="apple-touch-startup-image"/>
<link href="/images/default/pwa/splashes/splash-828x1792.png"
      media="(device-width: 414px) and (device-height: 896px) and (-webkit-device-pixel-ratio: 2)"
      rel="apple-touch-startup-image"/>
<link href="/images/default/pwa/splashes/splash-1242x2688.png"
      media="(device-width: 414px) and (device-height: 896px) and (-webkit-device-pixel-ratio: 3)"
      rel="apple-touch-startup-image"/>
<link href="/images/default/pwa/splashes/splash-1536x2048.png"
      media="(device-width: 768px) and (device-height: 1024px) and (-webkit-device-pixel-ratio: 2)"
      rel="apple-touch-startup-image"/>
<link href="/images/default/pwa/splashes/splash-1668x2224.png"
      media="(device-width: 834px) and (device-height: 1112px) and (-webkit-device-pixel-ratio: 2)"
      rel="apple-touch-startup-image"/>
<link href="/images/default/pwa/splashes/splash-1668x2388.png"
      media="(device-width: 834px) and (device-height: 1194px) and (-webkit-device-pixel-ratio: 2)"
      rel="apple-touch-startup-image"/>
<link href="/images/default/pwa/splashes/splash-2048x2732.png"
      media="(device-width: 1024px) and (device-height: 1366px) and (-webkit-device-pixel-ratio: 2)"
      rel="apple-touch-startup-image"/>

<!-- Tile for Win8 -->
<meta name="msapplication-TileColor" content="{{ $config['background_color'] }}">
<meta name="msapplication-TileImage" content="{{ $pwaIcon512 }}">

<script type="text/javascript">
    // Capture install prompt ASAP (fires before Vue boots)
    window.__ctcPwa = window.__ctcPwa || { deferredPrompt: null, installed: false };
    window.addEventListener('beforeinstallprompt', function (e) {
        e.preventDefault();
        window.__ctcPwa.deferredPrompt = e;
        window.dispatchEvent(new CustomEvent('ctc-pwa-prompt-ready'));
    });
    window.addEventListener('appinstalled', function () {
        window.__ctcPwa.deferredPrompt = null;
        window.__ctcPwa.installed = true;
        window.dispatchEvent(new CustomEvent('ctc-pwa-installed'));
    });

    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function () {
            navigator.serviceWorker.register('/serviceworker.js', {
                scope: '/',
                updateViaCache: 'none'
            }).catch(function () {});
        });
    }
</script>
