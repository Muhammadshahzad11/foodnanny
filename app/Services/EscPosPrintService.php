<?php

namespace App\Services;

use App\Enums\Ask;
use App\Models\Printer;
use Exception;
use Illuminate\Support\Facades\Log;

/**
 * Minimal ESC/POS network sender (TCP 9100).
 * Falls back gracefully when the printer is unreachable.
 */
class EscPosPrintService
{
    /**
     * Build ESC/POS bytes for a plain text document (no network send).
     */
    public function buildRawDocument(Printer $printer, string $text, bool $openDrawer = false): string
    {
        return $this->buildEscPosDocument(
            $text,
            (int) $printer->characters_per_line,
            $openDrawer || $printer->opensCashDrawer()
        );
    }

    /**
     * Base64 ESC/POS payload for client-side local print agent.
     */
    public function buildRawBase64(Printer $printer, string $text, bool $openDrawer = false): string
    {
        return base64_encode($this->buildRawDocument($printer, $text, $openDrawer));
    }

    /**
     * @throws Exception
     */
    public function printText(Printer $printer, string $text, bool $openDrawer = false): array
    {
        if (!$printer->isDirectPrint()) {
            return [
                'success' => false,
                'message' => trans('all.message.printer_not_direct'),
            ];
        }

        $ip   = trim((string) $printer->printer_ip);
        $port = (int) ($printer->printer_port ?: 9100);

        if ($ip === '' || $port <= 0) {
            throw new Exception(trans('all.message.printer_network_required'), 422);
        }

        $payload = $this->buildRawDocument($printer, $text, $openDrawer);

        return $this->sendRaw($ip, $port, $payload);
    }

    /**
     * Render KOT text body (shared by print + local bridge).
     */
    public function renderKotText(Printer $printer, array $payload): string
    {
        $width = max(24, min(64, (int) $printer->characters_per_line));
        $line  = str_repeat('-', $width);
        $rows  = [
            $this->center($payload['copy'] ?? 'KITCHEN KOT', $width),
            $this->center($payload['restaurant'] ?? '', $width),
            $line,
            'KOT#: ' . ($payload['kot_no'] ?? $payload['ticket_no'] ?? ''),
            'Order: ' . ($payload['order_serial_no'] ?? ''),
            'Type: ' . ($payload['order_type_label'] ?? ''),
            'Station: ' . ($payload['station'] ?? '-'),
            'Table: ' . ($payload['table_no'] ?? ($payload['table']['number'] ?? '-')),
            'Time: ' . ($payload['order_time'] ?? ''),
            $line,
        ];

        foreach ($payload['items'] ?? [] as $item) {
            $rows[] = ((int) ($item['quantity'] ?? 1)) . ' x ' . ($item['name'] ?? 'Item');
            foreach ($item['variation_lines'] ?? [] as $v) {
                $rows[] = '  - ' . $v;
            }
            foreach ($item['extra_lines'] ?? [] as $e) {
                $rows[] = '  + ' . $e;
            }
            if (!empty($item['instruction'])) {
                $rows[] = '  * ' . $item['instruction'];
            }
        }

        $rows[] = $line;
        $rows[] = 'Qty: ' . ($payload['total_qty'] ?? 0);
        if (!empty($payload['special_note'])) {
            $rows[] = 'Note: ' . $payload['special_note'];
        }
        $rows[] = $this->center($payload['printed_at'] ?? now()->toDateTimeString(), $width);
        $rows[] = '';
        $rows[] = '';

        return implode("\n", $rows);
    }

    /**
     * Render invoice text body (shared by print + local bridge).
     */
    public function renderInvoiceText(Printer $printer, array $payload): string
    {
        $width = max(24, min(64, (int) $printer->characters_per_line));
        $line  = str_repeat('-', $width);
        $rows  = [
            $this->center($payload['restaurant'] ?? 'INVOICE', $width),
            $line,
            'Bill#: ' . ($payload['order_serial_no'] ?? ''),
            'Type: ' . ($payload['order_type_label'] ?? ''),
            'Table: ' . ($payload['table_no'] ?? '-'),
            'Cashier: ' . ($payload['biller'] ?? ''),
            'Time: ' . ($payload['order_datetime'] ?? ''),
            $line,
        ];

        foreach ($payload['items'] ?? [] as $item) {
            $name = $item['name'] ?? 'Item';
            $qty  = (int) ($item['quantity'] ?? 1);
            $amt  = $item['total_price'] ?? $item['price'] ?? '';
            $rows[] = $qty . ' x ' . $name;
            if ($amt !== '') {
                $rows[] = $this->right((string) $amt, $width);
            }
        }

        $rows[] = $line;
        if (isset($payload['subtotal'])) {
            $rows[] = $this->pair('Subtotal', (string) $payload['subtotal'], $width);
        }
        if (isset($payload['tax'])) {
            $rows[] = $this->pair('Tax', (string) $payload['tax'], $width);
        }
        if (isset($payload['total'])) {
            $rows[] = $this->pair('TOTAL', (string) $payload['total'], $width);
        }
        $rows[] = $line;
        $rows[] = $this->center('Thank you', $width);
        $rows[] = '';
        $rows[] = '';

        return implode("\n", $rows);
    }

    /**
     * @throws Exception
     */
    public function test(Printer $printer): array
    {
        $width = max(24, min(64, (int) $printer->characters_per_line));
        $line  = str_repeat('-', $width);
        $body  = implode("\n", [
            $this->center('TEST PRINT', $width),
            $line,
            'Printer: ' . $printer->name,
            'IP: ' . ($printer->printer_ip ?: '-'),
            'Port: ' . ($printer->printer_port ?: 9100),
            'Time: ' . now()->format('Y-m-d H:i:s'),
            $line,
            $this->center('Connection OK', $width),
            '',
            '',
        ]);

        return $this->printText($printer, $body, (int) $printer->open_cash_drawer === Ask::YES);
    }

    /**
     * @throws Exception
     */
    public function printKot(Printer $printer, array $payload): array
    {
        return $this->printText($printer, $this->renderKotText($printer, $payload), false);
    }

    /**
     * ESC/POS base64 for KOT (for local print agent on POS PC).
     */
    public function kotRawBase64(Printer $printer, array $payload): string
    {
        return $this->buildRawBase64($printer, $this->renderKotText($printer, $payload), false);
    }

    /**
     * @throws Exception
     */
    public function printInvoice(Printer $printer, array $payload): array
    {
        return $this->printText($printer, $this->renderInvoiceText($printer, $payload), $printer->opensCashDrawer());
    }

    /**
     * ESC/POS base64 for invoice (for local print agent on POS PC).
     */
    public function invoiceRawBase64(Printer $printer, array $payload): string
    {
        return $this->buildRawBase64(
            $printer,
            $this->renderInvoiceText($printer, $payload),
            $printer->opensCashDrawer()
        );
    }

    /**
     * Quick TCP reachability check (no print payload).
     */
    public function isReachable(?string $ip, ?int $port = 9100, float $timeout = 1.2): bool
    {
        $ip = trim((string) $ip);
        $port = (int) ($port ?: 9100);
        if ($ip === '' || $port <= 0) {
            return false;
        }

        $errno  = 0;
        $errstr = '';
        $socket = @fsockopen($ip, $port, $errno, $errstr, $timeout);
        if (!$socket) {
            return false;
        }
        fclose($socket);

        return true;
    }

    protected function sendRaw(string $ip, int $port, string $payload): array
    {
        if (!$this->isReachable($ip, $port, 3)) {
            Log::info("ESC/POS connect failed {$ip}:{$port}");
            throw new Exception(trans('all.message.printer_connection_failed', [
                'ip'   => $ip,
                'port' => $port,
            ]), 422);
        }

        $errno  = 0;
        $errstr = '';
        $socket = @fsockopen($ip, $port, $errno, $errstr, 3);
        if (!$socket) {
            throw new Exception(trans('all.message.printer_connection_failed', [
                'ip'   => $ip,
                'port' => $port,
            ]), 422);
        }

        stream_set_timeout($socket, 5);
        $written = fwrite($socket, $payload);
        fclose($socket);

        if ($written === false) {
            throw new Exception(trans('all.message.printer_write_failed'), 422);
        }

        return [
            'success' => true,
            'message' => trans('all.message.printer_test_sent'),
            'bytes'   => $written,
        ];
    }

    protected function buildEscPosDocument(string $text, int $width, bool $openDrawer): string
    {
        $init   = "\x1B\x40"; // ESC @
        $body   = $this->wrapLines($text, $width);
        $cut    = "\n\n\n\x1D\x56\x00"; // GS V 0 full cut
        $drawer = $openDrawer ? "\x1B\x70\x00\x19\xFA" : ''; // ESC p

        return $init . $body . "\n" . $drawer . $cut;
    }

    protected function wrapLines(string $text, int $width): string
    {
        $out = [];
        foreach (preg_split("/\r\n|\n|\r/", $text) as $line) {
            if ($line === '') {
                $out[] = '';
                continue;
            }
            while (mb_strlen($line) > $width) {
                $out[] = mb_substr($line, 0, $width);
                $line  = mb_substr($line, $width);
            }
            $out[] = $line;
        }

        return implode("\n", $out);
    }

    protected function center(string $text, int $width): string
    {
        $text = trim($text);
        if ($text === '') {
            return '';
        }
        $len = mb_strlen($text);
        if ($len >= $width) {
            return mb_substr($text, 0, $width);
        }
        $pad = (int) floor(($width - $len) / 2);

        return str_repeat(' ', $pad) . $text;
    }

    protected function right(string $text, int $width): string
    {
        $text = trim($text);
        $len  = mb_strlen($text);
        if ($len >= $width) {
            return mb_substr($text, 0, $width);
        }

        return str_repeat(' ', $width - $len) . $text;
    }

    protected function pair(string $left, string $right, int $width): string
    {
        $left  = trim($left);
        $right = trim($right);
        $space = $width - mb_strlen($left) - mb_strlen($right);
        if ($space < 1) {
            return mb_substr($left . ' ' . $right, 0, $width);
        }

        return $left . str_repeat(' ', $space) . $right;
    }
}
