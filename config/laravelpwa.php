<?php

return [
    'name' => env('APP_NAME', 'Cost to Cost Foods'),
    'manifest' => [
        'name' => env('APP_NAME', 'Cost to Cost Foods'),
        'short_name' => env('PWA_SHORT_NAME', 'CTC Foods'),
        'description' => env('PWA_DESCRIPTION', 'Order food, manage tables, kitchen, and POS from Cost to Cost Foods.'),
        'start_url' => '/?source=pwa',
        'background_color' => env('PWA_BACKGROUND_COLOR', '#ffffff'),
        'theme_color' => env('PWA_THEME_COLOR', '#148A3C'),
        'display' => env('PWA_DISPLAY', 'standalone'),
        'orientation' => env('PWA_ORIENTATION', 'any'),
        'status_bar' => 'black-translucent',
        'icons' => [
            '72x72' => [
                'path' => env('D_72x72', '/images/default/pwa/icons/icon-72x72.png'),
                'purpose' => 'any'
            ],
            '96x96' => [
                'path' => env('D_96x96', '/images/default/pwa/icons/icon-96x96.png'),
                'purpose' => 'any'
            ],
            '128x128' => [
                'path' => env('D_128x128', '/images/default/pwa/icons/icon-128x128.png'),
                'purpose' => 'any'
            ],
            '144x144' => [
                'path' => env('D_144x144', '/images/default/pwa/icons/icon-144x144.png'),
                'purpose' => 'any'
            ],
            '152x152' => [
                'path' => env('D_152x152', '/images/default/pwa/icons/icon-152x152.png'),
                'purpose' => 'any'
            ],
            '192x192' => [
                'path' => env('D_192x192', '/images/default/pwa/icons/icon-192x192.png'),
                'purpose' => 'any'
            ],
            '384x384' => [
                'path' => env('D_384x384', '/images/default/pwa/icons/icon-384x384.png'),
                'purpose' => 'any'
            ],
            '512x512' => [
                'path' => env('D_512x512', '/images/default/pwa/icons/icon-512x512.png'),
                'purpose' => 'any'
            ],
        ],
        'splash' => [
            '640x1136' => env('D_640x1136', '/images/default/pwa/splashes/splash-640x1136.png'),
            '750x1334' => env('D_750x1334', '/images/default/pwa/splashes/splash-750x1334.png'),
            '828x1792' => env('D_828x1792', '/images/default/pwa/splashes/splash-828x1792.png'),
            '1125x2436' => env('D_1125x2436', '/images/default/pwa/splashes/splash-1125x2436.png'),
            '1242x2208' => env('D_1242x2208', '/images/default/pwa/splashes/splash-1242x2208.png'),
            '1242x2688' => env('D_1242x2688', '/images/default/pwa/splashes/splash-1242x2688.png'),
            '1536x2048' => env('D_1536x2048', '/images/default/pwa/splashes/splash-1536x2048.png'),
            '1668x2224' => env('D_1668x2224', '/images/default/pwa/splashes/splash-1668x2224.png'),
            '1668x2388' => env('D_1668x2388', '/images/default/pwa/splashes/splash-1668x2388.png'),
            '2048x2732' => env('D_2048x2732', '/images/default/pwa/splashes/splash-2048x2732.png'),
        ],
        'shortcuts' => [],
        'custom' => []
    ]
];
