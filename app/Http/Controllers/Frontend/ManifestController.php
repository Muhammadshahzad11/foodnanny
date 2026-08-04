<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\PwaManifestBuilder;
use Illuminate\Http\JsonResponse;

class ManifestController extends Controller
{
    public function __construct(private readonly PwaManifestBuilder $manifestBuilder)
    {
    }

    public function show(): JsonResponse
    {
        $manifest = $this->manifestBuilder->build();

        unset($manifest['cache_version'], $manifest['cache_strategy'], $manifest['status_bar']);

        return response()->json($manifest, 200, [
            'Content-Type' => 'application/manifest+json',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
        ]);
    }

    public function installConfig(): JsonResponse
    {
        return response()->json([
            'data' => $this->manifestBuilder->installConfig(),
        ], 200, [
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
        ]);
    }
}
