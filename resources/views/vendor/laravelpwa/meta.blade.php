@php
    try {
        $manifest = app(\App\Services\PwaManifestBuilder::class)->build();
    } catch (\Throwable $e) {
        $manifest = [
            'name' => config('app.name', 'Cost to Cost Foods'),
            'short_name' => 'App',
            'theme_color' => '#148A3C',
            'background_color' => '#FFFFFF',
            'display' => 'standalone',
            'status_bar' => 'black-translucent',
            'cache_version' => 1,
            'icons' => [],
        ];
    }

    $pwaCacheVersion = (int) ($manifest['cache_version'] ?? 1);
    $themeColor = $manifest['theme_color'] ?? '#148A3C';
    $bgColor = $manifest['background_color'] ?? '#FFFFFF';
    $shortName = $manifest['short_name'] ?? ($manifest['name'] ?? 'App');
    $display = $manifest['display'] ?? 'standalone';
    $statusBar = $manifest['status_bar'] ?? 'black-translucent';

    $pwaIcon192 = '/images/default/pwa/icons/icon-192x192.png?v=' . $pwaCacheVersion;
    $pwaIcon512 = '/images/default/pwa/icons/icon-512x512.png?v=' . $pwaCacheVersion;
    foreach (($manifest['icons'] ?? []) as $icon) {
        $sizes = (string) ($icon['sizes'] ?? '');
        $src = (string) ($icon['src'] ?? '');
        if ($sizes === '192x192' && $src !== '') {
            $pwaIcon192 = $src;
        }
        if ($sizes === '512x512' && $src !== '') {
            $pwaIcon512 = $src;
        }
    }

    $splashFallback = '/images/default/pwa/splashes/splash-2048x2732.png?v=' . $pwaCacheVersion;
    try {
        $pwaModel = \App\Models\Pwa::query()->first();
        if ($pwaModel && !empty($pwaModel->getFirstMediaUrl('pwa_splash'))) {
            $splashMedia = $pwaModel->getMedia('pwa_splash')->first();
            $splashUrl = $splashMedia?->getUrl('D_2048x2732') ?: $splashMedia?->getUrl();
            if ($splashUrl) {
                $parts = parse_url($splashUrl);
                if (!empty($parts['path'])) {
                    $splashFallback = $parts['path'] . '?v=' . $pwaCacheVersion;
                }
            }
        }
    } catch (\Throwable $e) {
    }
@endphp
<!-- Web Application Manifest (relative = same origin as current port) -->
<link rel="manifest" href="/manifest.json?v={{ $pwaCacheVersion }}">
<!-- Chrome for Android theme color -->
<meta name="theme-color" content="{{ $themeColor }}">

<!-- Add to homescreen for Chrome on Android -->
<meta name="mobile-web-app-capable" content="{{ $display == 'standalone' ? 'yes' : 'no' }}">
<meta name="application-name" content="{{ $shortName }}">
<link rel="icon" sizes="512x512" href="{{ $pwaIcon512 }}">

<!-- Add to homescreen for Safari on iOS -->
<meta name="apple-mobile-web-app-capable" content="{{ $display == 'standalone' ? 'yes' : 'no' }}">
<meta name="apple-mobile-web-app-status-bar-style" content="{{ $statusBar }}">
<meta name="apple-mobile-web-app-title" content="{{ $shortName }}">
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
<link href="{{ $splashFallback }}"
      media="(device-width: 1024px) and (device-height: 1366px) and (-webkit-device-pixel-ratio: 2)"
      rel="apple-touch-startup-image"/>

<!-- Tile for Win8 -->
<meta name="msapplication-TileColor" content="{{ $bgColor }}">
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
