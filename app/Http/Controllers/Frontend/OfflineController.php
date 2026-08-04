<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\PwaManifestBuilder;
use Illuminate\Contracts\View\View;

class OfflineController extends Controller
{
    public function __construct(private readonly PwaManifestBuilder $manifestBuilder)
    {
    }

    public function __invoke(): View
    {
        $config = $this->manifestBuilder->installConfig();

        return view('vendor.laravelpwa.offline', [
            'appName' => $config['name'],
            'themeColor' => $config['theme_color'],
            'icon' => $config['icon'],
        ]);
    }
}
