<?php

namespace App\Services;

use App\Enums\PwaCacheStrategy;
use App\Enums\PwaDisplayMode;
use App\Enums\PwaOrientation;
use App\Models\Pwa;
use Dipokhalder\Settings\Facades\Settings;

class PwaManifestBuilder
{
    public function build(): array
    {
        $pwa = Pwa::query()->first();
        $config = config('laravelpwa.manifest', []);
        $companyName = Settings::group('company')->get('company_name')
            ?: ($config['name'] ?? config('app.name', 'Cost to Cost Foods'));

        $name = $pwa?->name ?: $companyName;
        $shortName = $pwa?->short_name ?: mb_substr($name, 0, 12);
        $description = $pwa?->description
            ?: ($config['description'] ?? 'Order food, manage tables, kitchen, and POS from Cost to Cost Foods.');

        return [
            'id' => '/',
            'name' => $name,
            'short_name' => $shortName,
            'description' => $description,
            'start_url' => '/?source=pwa',
            'scope' => '/',
            'display' => $pwa?->display_mode ?: ($config['display'] ?? PwaDisplayMode::STANDALONE),
            'orientation' => $pwa?->orientation ?: ($config['orientation'] ?? PwaOrientation::ANY),
            'background_color' => $this->normalizeColor($pwa?->background_color ?: ($config['background_color'] ?? '#FFFFFF')),
            'theme_color' => $this->normalizeColor($pwa?->theme_color ?: ($config['theme_color'] ?? '#148A3C')),
            'status_bar' => $config['status_bar'] ?? 'black-translucent',
            'lang' => app()->getLocale() ?: 'en',
            'dir' => 'ltr',
            'categories' => ['food', 'business', 'lifestyle'],
            'prefer_related_applications' => false,
            'icons' => $this->icons($config, $pwa),
            'shortcuts' => $this->shortcuts(),
            'screenshots' => $this->screenshots(),
            'protocol_handlers' => [],
            'cache_version' => (int) ($pwa?->cache_version ?? 1),
            'cache_strategy' => $pwa?->cache_strategy ?: PwaCacheStrategy::BALANCED,
        ];
    }

    public function installConfig(): array
    {
        $pwa = Pwa::query()->first();
        $manifest = $this->build();

        return [
            'name' => $manifest['name'],
            'short_name' => $manifest['short_name'],
            'description' => $manifest['description'],
            'theme_color' => $manifest['theme_color'],
            'background_color' => $manifest['background_color'],
            // Always same-origin relative path so the popup icon loads on localhost AND 127.0.0.1
            'icon' => '/images/default/pwa/icons/icon-192x192.png',
            'enable_install_popup' => (bool) ($pwa?->enable_install_popup ?? true),
            'popup_delay_seconds' => (int) ($pwa?->popup_delay_seconds ?? 2),
            'popup_frequency_hours' => (int) ($pwa?->popup_frequency_hours ?? 24),
            'offline_mode' => (bool) ($pwa?->offline_mode ?? true),
            'auto_update' => (bool) ($pwa?->auto_update ?? true),
            'cache_strategy' => $manifest['cache_strategy'],
            'cache_version' => $manifest['cache_version'],
        ];
    }

    private function icons(array $config, ?Pwa $pwa): array
    {
        $defaults = [
            '72x72' => '/images/default/pwa/icons/icon-72x72.png',
            '96x96' => '/images/default/pwa/icons/icon-96x96.png',
            '128x128' => '/images/default/pwa/icons/icon-128x128.png',
            '144x144' => '/images/default/pwa/icons/icon-144x144.png',
            '152x152' => '/images/default/pwa/icons/icon-152x152.png',
            '192x192' => '/images/default/pwa/icons/icon-192x192.png',
            '384x384' => '/images/default/pwa/icons/icon-384x384.png',
            '512x512' => '/images/default/pwa/icons/icon-512x512.png',
        ];

        $icons = [];
        foreach ($defaults as $size => $fallback) {
            $src = $this->sameOriginPath($fallback);

            // Prefer uploaded icons only when they resolve to a same-origin relative /storage path
            if ($pwa && !empty($pwa->getFirstMediaUrl('pwa_icon'))) {
                try {
                    $media = $pwa->getMedia('pwa_icon')->first();
                    $url = $media?->getUrl('D_' . $size);
                    $relative = $this->toSameOriginRelative($url);
                    if ($relative) {
                        $src = $relative;
                    }
                } catch (\Throwable $e) {
                }
            } else {
                $configured = data_get($config, "icons.{$size}.path");
                $relative = $this->toSameOriginRelative($configured);
                if ($relative) {
                    $src = $relative;
                }
            }

            $icons[] = [
                'src' => $src,
                'sizes' => $size,
                'type' => 'image/png',
                'purpose' => 'any',
            ];
            $icons[] = [
                'src' => $src,
                'sizes' => $size,
                'type' => 'image/png',
                'purpose' => 'maskable',
            ];
        }

        return $icons;
    }

    private function shortcuts(): array
    {
        $iconSrc = '/images/default/pwa/icons/icon-192x192.png';
        $defaults = [
            ['name' => 'Orders', 'description' => 'Open orders', 'url' => '/admin/online-orders?source=pwa_shortcut'],
            ['name' => 'Tables', 'description' => 'Restaurant tables', 'url' => '/admin/tables?source=pwa_shortcut'],
            ['name' => 'Kitchen', 'description' => 'Kitchen queue', 'url' => '/admin/kitchen/queue?source=pwa_shortcut'],
            ['name' => 'Waiter', 'description' => 'Waiter dashboard', 'url' => '/admin/waiter?source=pwa_shortcut'],
            ['name' => 'POS', 'description' => 'Point of sale', 'url' => '/admin/pos?source=pwa_shortcut'],
            ['name' => 'Notifications', 'description' => 'Open dashboard notifications', 'url' => '/admin/dashboard?source=pwa_shortcut'],
        ];

        return array_map(static function (array $item) use ($iconSrc) {
            return [
                'name' => $item['name'],
                'short_name' => $item['name'],
                'description' => $item['description'],
                'url' => $item['url'],
                'icons' => [[
                    'src' => $iconSrc,
                    'sizes' => '192x192',
                    'type' => 'image/png',
                    'purpose' => 'any',
                ]],
            ];
        }, $defaults);
    }

    private function screenshots(): array
    {
        return [[
            'src' => '/images/default/pwa/splashes/splash-2048x2732.png',
            'sizes' => '2048x2732',
            'type' => 'image/png',
            'form_factor' => 'narrow',
            'label' => 'App splash',
        ]];
    }

    /**
     * Chrome requires manifest icons to be same-origin as the page.
     * Absolute http://127.0.0.1/... icons break install when the user opens localhost (and vice versa).
     */
    private function toSameOriginRelative(?string $url): ?string
    {
        if (!$url) {
            return null;
        }

        if (str_starts_with($url, '/') && !str_starts_with($url, '//')) {
            return $url;
        }

        $parts = parse_url($url);
        if (!empty($parts['path'])) {
            return $parts['path'] . (isset($parts['query']) ? '?' . $parts['query'] : '');
        }

        return null;
    }

    private function sameOriginPath(string $path): string
    {
        return str_starts_with($path, '/') ? $path : '/' . ltrim($path, '/');
    }

    private function normalizeColor(?string $color): string
    {
        $color = trim((string) $color);
        if ($color === '') {
            return '#148A3C';
        }
        if (!str_starts_with($color, '#')) {
            return '#' . ltrim($color, '#');
        }
        return $color;
    }
}
