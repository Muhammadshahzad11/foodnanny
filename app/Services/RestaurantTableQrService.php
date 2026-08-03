<?php

namespace App\Services;

use App\Enums\Status;
use App\Enums\TableStatus;
use App\Libraries\QueryExceptionLibrary;
use App\Models\RestaurantTable;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\SvgWriter;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use ZipArchive;

class RestaurantTableQrService
{
    public function __construct(
        protected RestaurantTableService $restaurantTableService
    ) {
    }

    public function publicScanUrl(string $token): string
    {
        return rtrim((string) config('app.url'), '/') . '/t/' . $token;
    }

    /**
     * @throws Exception
     */
    public function preview(RestaurantTable $restaurantTable): RestaurantTable
    {
        $this->restaurantTableService->assertCanManage($restaurantTable);
        $restaurantTable->load(['restaurant:id,name,slug,status']);

        return $restaurantTable;
    }

    /**
     * @throws Exception
     */
    public function generate(RestaurantTable $restaurantTable, bool $forceNewToken = false): RestaurantTable
    {
        try {
            $this->restaurantTableService->assertCanManage($restaurantTable);

            return DB::transaction(function () use ($restaurantTable, $forceNewToken) {
                $restaurantTable->refresh();

                if (empty($restaurantTable->uuid)) {
                    $restaurantTable->uuid = (string) Str::uuid();
                }

                $hadToken = filled($restaurantTable->qr_token);
                if ($forceNewToken || !$hadToken) {
                    $restaurantTable->qr_token = bin2hex(random_bytes(32));
                    if ($forceNewToken && $hadToken) {
                        $restaurantTable->qr_version = max(1, (int) $restaurantTable->qr_version) + 1;
                    } else {
                        $restaurantTable->qr_version = max(1, (int) $restaurantTable->qr_version);
                    }
                }

                $url = $this->publicScanUrl($restaurantTable->qr_token);
                $restaurantTable->qr_url = $url;
                $restaurantTable->qr_generated_at = now();
                $restaurantTable->save();

                $this->storeQrMedia($restaurantTable, $url);

                return $restaurantTable->fresh()->load(['restaurant:id,name,slug,status']);
            });
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(
                $exception->getCode() === 422 ? $exception->getMessage() : QueryExceptionLibrary::message($exception),
                422
            );
        }
    }

    /**
     * @throws Exception
     */
    public function regenerate(RestaurantTable $restaurantTable): RestaurantTable
    {
        return $this->generate($restaurantTable, true);
    }

    /**
     * @throws Exception
     */
    public function generateMissing(): Collection
    {
        try {
            $tables = RestaurantTable::query()
                ->where(function ($query) {
                    $query->whereNull('qr_token')->orWhere('qr_token', '');
                })
                ->get();

            $generated = collect();
            foreach ($tables as $table) {
                $generated->push($this->generate($table, false));
            }

            return $generated;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(
                $exception->getCode() === 422 ? $exception->getMessage() : QueryExceptionLibrary::message($exception),
                422
            );
        }
    }

    /**
     * @throws Exception
     */
    public function generateAll(): Collection
    {
        try {
            $tables = RestaurantTable::query()->get();
            $generated = collect();
            foreach ($tables as $table) {
                $generated->push($this->generate($table, blank($table->qr_token)));
            }

            return $generated;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(
                $exception->getCode() === 422 ? $exception->getMessage() : QueryExceptionLibrary::message($exception),
                422
            );
        }
    }

    /**
     * @throws Exception
     */
    public function regenerateSelected(array $ids): Collection
    {
        try {
            $tables = RestaurantTable::query()->whereIn('id', $ids)->get();
            if ($tables->count() !== count(array_unique($ids))) {
                throw new Exception(trans('all.message.table_qr_not_found'), 422);
            }

            $generated = collect();
            foreach ($tables as $table) {
                $generated->push($this->regenerate($table));
            }

            return $generated;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(
                $exception->getCode() === 422 ? $exception->getMessage() : QueryExceptionLibrary::message($exception),
                422
            );
        }
    }

    /**
     * @throws Exception
     */
    public function download(RestaurantTable $restaurantTable, string $format = 'png'): BinaryFileResponse|StreamedResponse
    {
        $this->restaurantTableService->assertCanManage($restaurantTable);

        if (!$restaurantTable->hasQr()) {
            $restaurantTable = $this->generate($restaurantTable);
        }

        $format = strtolower($format) === 'svg' ? 'svg' : 'png';
        $filename = $this->safeFilename($restaurantTable) . '.' . $format;
        $content = $this->buildQrBinary($restaurantTable->qr_url ?: $this->publicScanUrl($restaurantTable->qr_token), $format);

        return response()->streamDownload(function () use ($content) {
            echo $content;
        }, $filename, [
            'Content-Type' => $format === 'svg' ? 'image/svg+xml' : 'image/png',
        ]);
    }

    /**
     * @throws Exception
     */
    public function bulkDownload(array $ids): BinaryFileResponse
    {
        try {
            $tables = RestaurantTable::query()->with('restaurant:id,name')->whereIn('id', $ids)->get();
            if ($tables->isEmpty()) {
                throw new Exception(trans('all.message.table_qr_not_found'), 422);
            }

            foreach ($tables as $table) {
                $this->restaurantTableService->assertCanManage($table);
                if (!$table->hasQr()) {
                    $this->generate($table);
                    $table->refresh();
                }
            }

            $tempDir = storage_path('app/tmp/table-qr-' . Str::random(8));
            File::ensureDirectoryExists($tempDir);
            $zipPath = $tempDir . '/table-qr-codes.zip';

            $zip = new ZipArchive();
            if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
                throw new Exception(trans('all.message.table_qr_zip_failed'), 422);
            }

            foreach ($tables as $table) {
                $png = $this->buildQrBinary($table->qr_url ?: $this->publicScanUrl($table->qr_token), 'png');
                $zip->addFromString($this->safeFilename($table) . '.png', $png);
            }
            $zip->close();

            return response()->download($zipPath, 'table-qr-codes.zip', [
                'Content-Type' => 'application/zip',
            ])->deleteFileAfterSend(true);
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(
                $exception->getCode() === 422 ? $exception->getMessage() : QueryExceptionLibrary::message($exception),
                422
            );
        }
    }

    /**
     * @throws Exception
     */
    public function printData(array $ids): array
    {
        $tables = RestaurantTable::query()
            ->with('restaurant:id,name,slug')
            ->whereIn('id', $ids)
            ->orderBy('table_number')
            ->get();

        if ($tables->isEmpty()) {
            throw new Exception(trans('all.message.table_qr_not_found'), 422);
        }

        $items = [];
        foreach ($tables as $table) {
            $this->restaurantTableService->assertCanManage($table);
            if (!$table->hasQr()) {
                $table = $this->generate($table);
            }

            $png = $this->buildQrBinary($table->qr_url ?: $this->publicScanUrl($table->qr_token), 'png');
            $items[] = [
                'id'            => $table->id,
                'uuid'          => $table->uuid,
                'table_number'  => $table->table_number,
                'name'          => $table->name,
                'zone'          => $table->zone,
                'restaurant'    => $table->restaurant?->name,
                'qr_url'        => $table->qr_url,
                'qr_image_base64' => 'data:image/png;base64,' . base64_encode($png),
            ];
        }

        return [
            'generated_at' => now()->toDateTimeString(),
            'count'        => count($items),
            'items'        => $items,
            'instruction'  => trans('all.message.table_qr_scan_instruction'),
        ];
    }

    /**
     * Public resolve — no restaurant scope (guest).
     *
     * @throws Exception
     */
    public function resolveByToken(string $token): array
    {
        $token = trim($token);
        if ($token === '' || !preg_match('/^[a-f0-9]{64}$/', $token)) {
            throw new Exception(trans('all.message.table_qr_invalid'), 404);
        }

        $table = RestaurantTable::withoutGlobalScopes()
            ->withTrashed()
            ->with(['restaurant:id,name,slug,status,current_status'])
            ->where('qr_token', $token)
            ->first();

        if (!$table) {
            throw new Exception(trans('all.message.table_qr_invalid'), 404);
        }

        if ($table->trashed()) {
            throw new Exception(trans('all.message.table_qr_removed'), 404);
        }

        if (in_array((int) $table->status, [TableStatus::INACTIVE, TableStatus::OUT_OF_SERVICE], true)) {
            throw new Exception(trans('all.message.table_qr_inactive'), 404);
        }

        if (!$table->restaurant || (int) $table->restaurant->status === Status::INACTIVE) {
            throw new Exception(trans('all.message.restaurant_inactive_for_qr'), 404);
        }

        if ((int) $table->restaurant->current_status === Status::INACTIVE) {
            throw new Exception(trans('all.message.restaurant_closed_for_qr'), 404);
        }

        return [
            'table_id'         => $table->id,
            'table_uuid'       => $table->uuid,
            'table_number'     => $table->table_number,
            'table_name'       => $table->name,
            'zone'             => $table->zone,
            'status'           => $table->status,
            'qr_token'         => $table->qr_token,
            'qr_version'       => $table->qr_version,
            'restaurant_id'    => $table->restaurant->id,
            'restaurant_slug'  => $table->restaurant->slug,
            'restaurant_name'  => $table->restaurant->name,
            'menu_path'        => '/restaurant/' . $table->restaurant->slug,
            'error_code'       => null,
            'dine_in_context'  => [
                'source'          => 'table_qr',
                'table_id'        => $table->id,
                'table_uuid'      => $table->uuid,
                'qr_token'        => $table->qr_token,
                'qr_version'      => $table->qr_version,
                'restaurant_id'   => $table->restaurant->id,
                'restaurant_slug' => $table->restaurant->slug,
            ],
        ];
    }

    protected function storeQrMedia(RestaurantTable $restaurantTable, string $url): void
    {
        $png = $this->buildQrBinary($url, 'png');
        $tempPath = storage_path('app/tmp/qr-' . $restaurantTable->uuid . '-' . Str::random(6) . '.png');
        File::ensureDirectoryExists(dirname($tempPath));
        File::put($tempPath, $png);

        $restaurantTable->clearMediaCollection(RestaurantTable::MEDIA_COLLECTION_QR);
        $restaurantTable
            ->addMedia($tempPath)
            ->usingFileName($this->safeFilename($restaurantTable) . '.png')
            ->toMediaCollection(RestaurantTable::MEDIA_COLLECTION_QR);

        if (File::exists($tempPath)) {
            File::delete($tempPath);
        }
    }

    protected function buildQrBinary(string $data, string $format = 'png'): string
    {
        $writer = $format === 'svg' ? new SvgWriter() : new PngWriter();

        $result = Builder::create()
            ->writer($writer)
            ->writerOptions([])
            ->data($data)
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(ErrorCorrectionLevel::High)
            ->size(512)
            ->margin(16)
            ->roundBlockSizeMode(RoundBlockSizeMode::Margin)
            ->build();

        return $result->getString();
    }

    protected function safeFilename(RestaurantTable $restaurantTable): string
    {
        $base = ($restaurantTable->restaurant?->name ? $restaurantTable->restaurant->name . '-' : '')
            . 'table-' . $restaurantTable->table_number;

        return Str::slug($base) ?: ('table-' . $restaurantTable->uuid);
    }
}
