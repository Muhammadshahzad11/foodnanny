<?php

namespace App\Services;

use App\Enums\Ask;
use App\Enums\PrintFormat;
use App\Enums\PrinterType;
use App\Enums\PrintingChoice;
use App\Enums\Status;
use App\Http\Requests\PaginateRequest;
use App\Http\Requests\PrinterRequest;
use App\Libraries\QueryExceptionLibrary;
use App\Models\Printer;
use App\Traits\DefaultAccessModelTrait;
use Exception;
use Illuminate\Support\Facades\Log;

class PrinterService
{
    use DefaultAccessModelTrait;

    protected array $filter = [
        'name',
        'printing_choice',
        'print_format',
        'printer_type',
        'status',
    ];

    /**
     * Active printers with live IP connection status (for POS / KOT screens).
     *
     * @throws Exception
     */
    public function fetchConnected(?string $format = null)
    {
        try {
            $query = Printer::query()
                ->with('restaurant')
                ->withCount('kitchens')
                ->where('status', Status::ACTIVE)
                ->orderBy('print_format')
                ->orderBy('name');

            if ($format === 'kot') {
                $query->where('print_format', PrintFormat::KOT);
            } elseif ($format === 'invoice') {
                $query->where('print_format', PrintFormat::INVOICE);
            }

            $printers = $query->get();

            return $this->attachConnectionStatus($printers);
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * Probe TCP connectivity for direct/network printers.
     *
     * @param  \Illuminate\Support\Collection|iterable  $printers
     * @return \Illuminate\Support\Collection
     */
    public function attachConnectionStatus($printers)
    {
        $escPos = app(EscPosPrintService::class);

        return collect($printers)->map(function (Printer $printer) use ($escPos) {
            $choice = (int) $printer->printing_choice;
            if ($choice === PrintingChoice::BROWSER_POPUP) {
                $printer->setAttribute('connection_status', 'browser');
                $printer->setAttribute('is_connected', true);
                $printer->setAttribute('connection_label', 'browser_popup');
            } elseif (blank($printer->printer_ip)) {
                $printer->setAttribute('connection_status', 'offline');
                $printer->setAttribute('is_connected', false);
                $printer->setAttribute('connection_label', 'ip_missing');
            } elseif ($this->isLanOrLocalAgentTarget($printer)) {
                // Cloud server cannot TCP-probe restaurant LAN IPs — Local Print Agent does.
                $printer->setAttribute('connection_status', 'local_agent');
                $printer->setAttribute('is_connected', true);
                $printer->setAttribute('connection_label', 'local_agent_ready');
            } else {
                $ok = $escPos->isReachable($printer->printer_ip, (int) ($printer->printer_port ?: 9100));
                $printer->setAttribute('connection_status', $ok ? 'connected' : 'offline');
                $printer->setAttribute('is_connected', $ok);
                $printer->setAttribute('connection_label', $ok ? 'ip_connected' : 'ip_offline');
            }

            return $printer;
        });
    }

    /**
     * @throws Exception
     */
    public function list(PaginateRequest $request)
    {
        try {
            $requests    = $request->all();
            $method      = $request->get('paginate', 0) == 1 ? 'paginate' : 'get';
            $methodValue = $request->get('paginate', 0) == 1 ? $request->get('per_page', 10) : '*';
            $orderColumn = $request->get('order_column') ?? 'id';
            $orderType   = $request->get('order_type') ?? 'desc';
            $withStatus  = (int) $request->get('with_connection', 0) === 1;

            $result = Printer::query()
                ->with('restaurant')
                ->withCount('kitchens')
                ->where(function ($query) use ($requests) {
                    foreach ($requests as $key => $value) {
                        if (in_array($key, $this->filter, true)) {
                            $query->where($key, 'like', '%' . $value . '%');
                        }
                    }
                })
                ->orderBy($orderColumn, $orderType)
                ->$method($methodValue);

            if ($withStatus) {
                if (method_exists($result, 'getCollection')) {
                    $result->setCollection($this->attachConnectionStatus($result->getCollection()));
                } else {
                    $result = $this->attachConnectionStatus($result);
                }
            }

            return $result;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function store(PrinterRequest $request): Printer
    {
        try {
            $data = $this->normalizePayload($request->validated());

            return Printer::create($data + [
                'restaurant_id' => $this->restaurant(),
            ]);
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function update(PrinterRequest $request, Printer $printer): Printer
    {
        try {
            $printer->update($this->normalizePayload($request->validated()));

            return $printer->fresh();
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function destroy(Printer $printer): void
    {
        try {
            if ($printer->kitchens()->exists()) {
                throw new Exception(trans('all.message.printer_assigned_to_kitchen'), 422);
            }
            $printer->delete();
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
    public function testPrint(Printer $printer): array
    {
        try {
            if ((int) $printer->printing_choice === PrintingChoice::BROWSER_POPUP) {
                return [
                    'mode'    => 'browser_popup',
                    'success' => true,
                    'message' => trans('all.message.printer_test_browser_popup'),
                    'payload' => [
                        'copy'       => 'TEST PRINT',
                        'printer'    => $printer->name,
                        'printed_at' => now()->toDateTimeString(),
                        'lines'      => [
                            'Printer configuration OK',
                            'Browser Popup mode',
                            'Restaurant ID: ' . $printer->restaurant_id,
                        ],
                    ],
                ];
            }

            $escPos = app(EscPosPrintService::class);
            $width  = max(24, min(64, (int) $printer->characters_per_line));
            $line   = str_repeat('-', $width);
            $rawBase64 = $escPos->buildRawBase64($printer, implode("\n", [
                'TEST PRINT',
                $line,
                'Printer: ' . $printer->name,
                'IP: ' . ($printer->printer_ip ?: '-'),
                'Port: ' . ($printer->printer_port ?: 9100),
                'Time: ' . now()->format('Y-m-d H:i:s'),
                $line,
                'Connection OK',
                '',
                '',
            ]), (int) $printer->open_cash_drawer === Ask::YES);

            try {
                $result = $escPos->test($printer);

                return [
                    'mode'          => 'direct_print',
                    'success'       => (bool) ($result['success'] ?? false),
                    'message'       => $result['message'] ?? trans('all.message.printer_test_sent'),
                    'raw_base64'    => $rawBase64,
                    'computer_ipv4' => $printer->computer_ipv4,
                    'printer_ip'    => $printer->printer_ip,
                    'printer_port'  => (int) ($printer->printer_port ?: 9100),
                    'bridge_port'   => 1811,
                ];
            } catch (Exception $exception) {
                // Cloud server cannot reach LAN — POS PC local agent must print
                return [
                    'mode'          => 'local_bridge',
                    'success'       => false,
                    'message'       => trans('all.message.printer_use_local_agent'),
                    'raw_base64'    => $rawBase64,
                    'computer_ipv4' => $printer->computer_ipv4,
                    'printer_ip'    => $printer->printer_ip,
                    'printer_port'  => (int) ($printer->printer_port ?: 9100),
                    'bridge_port'   => 1811,
                    'server_error'  => $exception->getMessage(),
                ];
            }
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception($exception->getMessage(), 422);
        }
    }

    protected function normalizePayload(array $data): array
    {
        $choice = (int) ($data['printing_choice'] ?? PrintingChoice::BROWSER_POPUP);

        if ($choice !== PrintingChoice::DIRECT_PRINT) {
            $data['computer_ipv4'] = null;
            $data['printer_ip']    = null;
            $data['printer_port']  = null;
        } else {
            $data['printer_port'] = (int) ($data['printer_port'] ?? 9100);
            $data['printer_type'] = (int) ($data['printer_type'] ?? PrinterType::NETWORK);
        }

        $data['characters_per_line'] = (int) ($data['characters_per_line'] ?? 42);
        $data['open_cash_drawer']    = (int) ($data['open_cash_drawer'] ?? Ask::NO);
        $data['invoice_qr_status']   = (int) ($data['invoice_qr_status'] ?? Ask::NO);
        $data['print_format']        = (int) ($data['print_format'] ?? PrintFormat::KOT);
        $data['status']              = (int) ($data['status'] ?? Status::ACTIVE);

        return $data;
    }

    /**
     * Private / restaurant LAN targets are only reachable from the POS PC local agent.
     */
    protected function isLanOrLocalAgentTarget(Printer $printer): bool
    {
        if (filled($printer->computer_ipv4)) {
            return true;
        }

        $ip = trim((string) $printer->printer_ip);

        return $this->isPrivateIp($ip);
    }

    protected function isPrivateIp(string $ip): bool
    {
        if ($ip === '' || filter_var($ip, FILTER_VALIDATE_IP) === false) {
            return false;
        }

        return !filter_var(
            $ip,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
        );
    }
}
