<?php

namespace App\Services;

use App\Enums\Ask;
use App\Models\Printer;
use Exception;
use Illuminate\Support\Facades\Log;

/**
 * ESC/POS builder for thermal KOT + invoice (TCP 9100 / local agent).
 * Prints logo (raster), tagline, Powered by, and Indian Rupee glyph (bitmap).
 */
class EscPosPrintService
{
    /** Placeholder replaced with ₹ raster when emitting bytes */
    private const RUPEE = "\x01";

    private static ?string $rupeeRasterCache = null;

    public function buildRawDocument(Printer $printer, string $text, bool $openDrawer = false): string
    {
        return $this->buildEscPosDocument(
            $text,
            $this->effectiveWidth($printer),
            $openDrawer || $printer->opensCashDrawer(),
            true
        );
    }

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

        return $this->sendRaw($ip, $port, $this->buildRawDocument($printer, $text, $openDrawer));
    }

    public function renderKotText(Printer $printer, array $payload): string
    {
        $width = $this->effectiveWidth($printer);
        $line  = str_repeat('-', $width);
        $rows  = [];

        // First two non-dash lines are printed double-size/centred by buildKotDocument.
        $rows[] = $this->center($payload['copy'] ?? 'KOT', $width);
        $rows[] = $this->center($this->ascii((string) ($payload['restaurant'] ?? '')), $width);
        $rows[] = $line;

        $table = (string) ($payload['table_no'] ?? ($payload['table']['number'] ?? ''));
        $left  = $table !== '' && $table !== '-'
            ? 'Table: ' . $table
            : (!empty($payload['counter']) ? $this->ascii((string) $payload['counter']) . ' Counter' : 'Counter');
        $rows[] = $this->pair($left, $this->ascii((string) ($payload['order_type_label'] ?? '')), $width);

        $stamp  = trim(((string) ($payload['order_date'] ?? '')) . ' ' . ((string) ($payload['order_time'] ?? '')));
        $ticket = (string) ($payload['ticket_no'] ?? ($payload['kot_no'] ? 'KOT - ' . $payload['kot_no'] : ''));
        $rows[] = $this->pair($stamp, $ticket, $width);
        if (!empty($payload['station'])) {
            $rows[] = $this->pair('Station', $this->ascii((string) $payload['station']), $width);
        }

        $rows[] = $line;
        $rows[] = $this->pair('ITEM', 'QTY', $width);
        $rows[] = $line;

        $index = 0;
        foreach ($payload['items'] ?? [] as $item) {
            $index++;
            $qty  = (int) ($item['quantity'] ?? 1);
            $name = $this->ascii((string) ($item['name'] ?? 'Item'));

            if (!empty($item['change_label']) || !empty($item['direction'])) {
                $dir   = strtoupper((string) ($item['direction'] ?? ''));
                $label = $dir === 'REMOVE' || $dir === 'ADD'
                    ? $dir . ': ' . $qty
                    : $this->ascii((string) $item['change_label']);
                foreach ($this->numberedRows($index, $name, $label, $width) as $row) {
                    $rows[] = $row;
                }
            } else {
                foreach ($this->numberedRows($index, $name, (string) $qty, $width) as $row) {
                    $rows[] = $row;
                }
            }

            foreach ($item['variation_lines'] ?? [] as $v) {
                $rows[] = '   - ' . $this->ascii((string) $v);
            }
            foreach ($item['extra_lines'] ?? [] as $e) {
                $rows[] = '   + ' . $this->ascii((string) $e);
            }
            if (!empty($item['instruction'])) {
                $rows[] = '   * ' . $this->ascii((string) $item['instruction']);
            }
        }

        $rows[] = $line;
        $rows[] = $this->pair('', 'Total: ' . ($payload['total_qty'] ?? 0) . ' items', $width);
        if (!empty($payload['special_note'])) {
            $rows[] = '';
            foreach ($this->wrapWords('NOTE: ' . $this->ascii((string) $payload['special_note']), $width) as $w) {
                $rows[] = $w;
            }
        }
        if (!empty($payload['powered_by'])) {
            $rows[] = '';
            $rows[] = $this->center('Powered by ' . $this->ascii((string) $payload['powered_by']), $width);
        }
        $rows[] = '';
        $rows[] = '';
        $rows[] = '';
        $rows[] = '';

        return implode("\n", $rows);
    }

    /**
     * "1. Item name" on the left with a right-aligned value, wrapping long names.
     *
     * @return string[]
     */
    protected function numberedRows(int $index, string $name, string $right, int $width): array
    {
        $prefix   = $index . '. ';
        $bodyWide = max(8, $width - $this->visibleLen($right) - 1);
        $wrapped  = $this->wrapWords($prefix . $name, $bodyWide);
        if (!$wrapped) {
            return [$this->pair($prefix, $right, $width)];
        }

        $rows   = [];
        $rows[] = $this->pair(array_shift($wrapped), $right, $width);
        foreach ($wrapped as $extra) {
            $rows[] = '   ' . $extra;
        }

        return $rows;
    }

    /**
     * Fixed-width invoice item row: name | qty | price | amount.
     *
     * @return string[]
     */
    protected function invoiceItemRows(string $name, string $qty, string $price, string $amount, int $width): array
    {
        $amtW   = min(11, max(8, (int) floor($width * 0.24)));
        $priceW = min(10, max(7, (int) floor($width * 0.21)));
        $qtyW   = 4;
        $nameW  = max(10, $width - $amtW - $priceW - $qtyW);

        $wrapped = $this->wrapWords($name, $nameW);
        if (!$wrapped) {
            $wrapped = [''];
        }

        $rows = [];
        $rows[] = str_pad($this->clipLine(array_shift($wrapped), $nameW), $nameW)
            . str_pad($qty, $qtyW, ' ', STR_PAD_LEFT)
            . str_pad($price, $priceW, ' ', STR_PAD_LEFT)
            . str_pad($amount, $amtW, ' ', STR_PAD_LEFT);

        foreach ($wrapped as $extra) {
            $rows[] = '  ' . $extra;
        }

        return $rows;
    }

    public function renderInvoiceText(Printer $printer, array $payload): string
    {
        $width = $this->effectiveWidth($printer);
        $line  = str_repeat('-', $width);
        $rows  = [];

        $rows[] = $this->center($this->ascii((string) ($payload['restaurant'] ?? 'INVOICE')), $width);
        $rows[] = $this->center('TAX INVOICE', $width);
        if (!empty($payload['tagline'])) {
            foreach ($this->wrapWords($this->ascii((string) $payload['tagline']), $width) as $w) {
                $rows[] = $this->center($w, $width);
            }
        }
        if (!empty($payload['phone'])) {
            $rows[] = $this->center('ph: ' . $this->ascii((string) $payload['phone']), $width);
        }
        $rows[] = $line;

        $customer = trim($this->ascii((string) ($payload['customer'] ?? '')));
        $rows[] = 'Name: ' . ($customer !== '' ? $customer : 'Walk-in');
        $rows[] = str_repeat('- ', (int) floor($width / 2));

        $date      = (string) ($payload['order_date'] ?? $payload['order_datetime'] ?? '');
        $time      = (string) ($payload['order_time'] ?? '');
        $table     = (string) ($payload['table_no'] ?? '');
        $typeLabel = $this->ascii((string) ($payload['order_type_label'] ?? ''));

        $rows[] = $this->pair('Date: ' . $date, 'Bill#: ' . ($payload['order_serial_no'] ?? ''), $width);
        $rows[] = $this->pair(
            'Time: ' . $time,
            $table !== '' && $table !== '-' ? 'Table: ' . $table : $typeLabel,
            $width
        );
        if (!empty($payload['biller'])) {
            $rows[] = $this->pair(
                'Cashier: ' . $this->ascii((string) $payload['biller']),
                $table !== '' && $table !== '-' ? $typeLabel : '',
                $width
            );
        }

        $rows[] = $line;
        foreach ($this->invoiceItemRows('Item', 'Qty', 'Price', 'Amt', $width) as $row) {
            $rows[] = $row;
        }
        $rows[] = $line;

        $totalQty = 0;
        foreach ($payload['items'] ?? [] as $item) {
            $name = $this->ascii((string) ($item['name'] ?? 'Item'));
            $qty  = (int) ($item['quantity'] ?? 1);
            $totalQty += $qty;

            $unit = $item['price'] ?? null;
            $amt  = $item['total_price'] ?? $item['price'] ?? 0;

            foreach ($this->invoiceItemRows(
                $name,
                (string) $qty,
                $unit === null ? '' : $this->moneyNumber($unit),
                $this->moneyNumber($amt),
                $width
            ) as $row) {
                $rows[] = $row;
            }

            foreach ($item['variation_lines'] ?? [] as $v) {
                $rows[] = '  ' . $this->ascii((string) $v);
            }
            foreach ($item['extra_lines'] ?? [] as $e) {
                $rows[] = '  +' . $this->ascii((string) $e);
            }
        }

        $rows[] = $line;

        $subtotal = (float) $this->moneyNumber($payload['subtotal'] ?? 0);
        $tax      = (float) $this->moneyNumber($payload['tax'] ?? 0);
        $discount = (float) $this->moneyNumber($payload['discount'] ?? 0);
        $total    = (float) $this->moneyNumber($payload['total'] ?? 0);

        $rows[] = $this->pair(
            'Total Qty ' . ($payload['total_qty'] ?? $totalQty),
            'Sub Total. ' . $this->thermalMoney($subtotal),
            $width
        );
        if ($discount > 0) {
            $rows[] = $this->pair('Discount', '-' . $this->thermalMoney($discount), $width);
        }
        if ($tax > 0) {
            // Stored as one GST figure — printed as the usual CGST + SGST halves.
            $half  = $tax / 2;
            $label = $this->gstHalfLabel($payload);
            $rows[] = $this->pair('CGST' . $label, $this->thermalMoney($half), $width);
            $rows[] = $this->pair('SGST' . $label, $this->thermalMoney($tax - $half), $width);
        }

        $roundOff = round($total - ($subtotal - $discount + $tax), 2);
        if (abs($roundOff) >= 0.01) {
            $rows[] = $this->pair('Round Off', ($roundOff < 0 ? '-' : '') . $this->thermalMoney(abs($roundOff)), $width);
        }

        $rows[] = $line;
        $rows[] = $this->pair('GRAND TOTAL', $this->thermalMoney($total), $width);
        if (!empty($payload['payment_label'])) {
            $rows[] = $this->pair('Payment', strtoupper($this->ascii((string) $payload['payment_label'])), $width);
        }
        $rows[] = $line;
        $rows[] = $this->center('Thank you for dining with us!', $width);
        $rows[] = $this->center('Visit again.', $width);
        if (!empty($payload['powered_by'])) {
            $rows[] = '';
            $rows[] = $this->center('Powered by ' . $this->ascii((string) $payload['powered_by']), $width);
        }
        // Extra blank lines so footer is not cut off by the autocutter
        $rows[] = '';
        $rows[] = '';
        $rows[] = '';
        $rows[] = '';
        $rows[] = '';

        return implode("\n", $rows);
    }

    /**
     * " (2.5%)" when the GST rate is known, otherwise no rate in the label.
     */
    protected function gstHalfLabel(array $payload): string
    {
        $rate = (float) ($payload['gst_rate'] ?? 0);
        if ($rate <= 0) {
            return '';
        }
        $half = rtrim(rtrim(number_format($rate / 2, 2, '.', ''), '0'), '.');

        return ' (' . $half . '%)';
    }

    /**
     * @throws Exception
     */
    public function test(Printer $printer): array
    {
        $width = $this->effectiveWidth($printer);
        $line  = str_repeat('-', $width);
        $body  = implode("\n", [
            $this->center('TEST PRINT', $width),
            $line,
            $this->pair('Sample', $this->thermalMoney(123.45), $width),
            $this->center('Logo + Rupee + Powered by', $width),
            $line,
            $this->center('Powered by', $width),
            $this->center('FoodNanny', $width),
            '',
            '',
        ]);

        $doc = $this->buildEscPosDocument($body, $width, (int) $printer->open_cash_drawer === Ask::YES, true);
        $ip  = trim((string) $printer->printer_ip);
        $port = (int) ($printer->printer_port ?: 9100);
        if ($ip === '') {
            return ['success' => true, 'message' => 'built', 'bytes' => strlen($doc)];
        }

        return $this->sendRaw($ip, $port, $doc);
    }

    /**
     * @throws Exception
     */
    public function printKot(Printer $printer, array $payload): array
    {
        return $this->sendRaw(
            trim((string) $printer->printer_ip),
            (int) ($printer->printer_port ?: 9100),
            $this->buildKotDocument($printer, $payload)
        );
    }

    public function kotRawBase64(Printer $printer, array $payload): string
    {
        return base64_encode($this->buildKotDocument($printer, $payload));
    }

    /**
     * @throws Exception
     */
    public function printInvoice(Printer $printer, array $payload): array
    {
        return $this->sendRaw(
            trim((string) $printer->printer_ip),
            (int) ($printer->printer_port ?: 9100),
            $this->buildInvoiceDocument($printer, $payload)
        );
    }

    public function invoiceRawBase64(Printer $printer, array $payload): string
    {
        return base64_encode($this->buildInvoiceDocument($printer, $payload));
    }

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
        if ($ip === '' || $port <= 0) {
            throw new Exception(trans('all.message.printer_network_required'), 422);
        }
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

    protected function buildKotDocument(Printer $printer, array $payload): string
    {
        $width = $this->effectiveWidth($printer);
        $out   = $this->escInit(true);
        // KOT is kitchen-only — never print restaurant logo (logo is for customer invoice only)
        $text  = $this->renderKotText($printer, $payload);
        $lines = preg_split("/\r\n|\n|\r/", $text) ?: [];
        $printedHeader = 0;

        foreach ($lines as $line) {
            $line = (string) $line;
            if ($printedHeader < 2 && $line !== '' && !str_starts_with($line, '-')) {
                $out .= "\x1B\x61\x01\x1B\x45\x01\x1D\x21\x11";
                $out .= $this->emitLine($this->clipLine($line, (int) floor($width / 2)));
                $out .= "\x1D\x21\x00\x1B\x45\x00\x1B\x61\x00";
                $printedHeader++;
                continue;
            }
            if (preg_match('/^\d+\s*(x|\.)\s+/', $line)) {
                $out .= "\x1B\x45\x01\x1D\x21\x01";
                $out .= $this->emitLine($this->clipLine($line, $width));
                $out .= "\x1D\x21\x00\x1B\x45\x00";
                continue;
            }
            $out .= $this->emitLine($this->clipLine($line, $width));
        }

        $out .= "\n\n\n";
        // Feed paper past cutter, then partial cut (keeps "Powered by" on the slip)
        $out .= "\x1B\x64\x08";   // ESC d 8 — advance 8 lines
        $out .= "\x1D\x56\x42\x00"; // GS V 66 0 — feed then cut

        return $out;
    }

    protected function buildInvoiceDocument(Printer $printer, array $payload): string
    {
        $width = $this->effectiveWidth($printer);
        $out   = $this->escInit(true);
        $out  .= $this->logoBlock($payload['logo_url'] ?? null, min(384, $width * 10));
        $out  .= $this->buildEscPosBody($this->renderInvoiceText($printer, $payload), $width);
        if ($printer->opensCashDrawer()) {
            $out .= "\x1B\x70\x00\x19\xFA";
        }
        $out .= "\n\n\n";
        $out .= "\x1B\x64\x08";
        $out .= "\x1D\x56\x42\x00";

        return $out;
    }

    protected function buildEscPosDocument(string $text, int $width, bool $openDrawer, bool $withMargin = true): string
    {
        $out  = $this->escInit($withMargin);
        $out .= $this->buildEscPosBody($text, $width);
        if ($openDrawer) {
            $out .= "\x1B\x70\x00\x19\xFA";
        }
        $out .= "\n\n\n";
        $out .= "\x1B\x64\x08";
        $out .= "\x1D\x56\x42\x00";

        return $out;
    }

    protected function escInit(bool $withMargin): string
    {
        $out = "\x1B\x40";     // init
        $out .= "\x1B\x74\x00"; // PC437 for ASCII text
        // Small left margin only — large margin + narrow CPL caused empty right side
        if ($withMargin) {
            $out .= "\x1D\x4C\x08\x00";
        }
        $out .= "\x1B\x33\x2C"; // line spacing

        return $out;
    }

    protected function effectiveWidth(Printer $printer): int
    {
        // RP3200 is typically 80mm (~42-48 chars). Avoid 32 (looks left-aligned with empty right).
        $configured = (int) ($printer->characters_per_line ?: 42);

        return max(32, min(48, $configured > 0 ? $configured : 42));
    }

    protected function buildEscPosBody(string $text, int $width): string
    {
        $out = '';
        foreach (preg_split("/\r\n|\n|\r/", $text) ?: [] as $line) {
            $out .= $this->emitLine($this->clipLine($line, $width));
        }

        return $out;
    }

    /**
     * Expand rupee placeholders into bitmap glyph + ASCII digits.
     */
    protected function emitLine(string $line): string
    {
        if (!str_contains($line, self::RUPEE)) {
            return $line . "\n";
        }
        $out  = '';
        $parts = explode(self::RUPEE, $line);
        foreach ($parts as $i => $part) {
            $out .= $part;
            if ($i < count($parts) - 1) {
                $out .= $this->rupeeRaster();
            }
        }

        return $out . "\n";
    }

    /**
     * Indian Rupee as ESC/POS raster (thermal printers cannot print UTF-8 ₹ as text).
     */
    protected function rupeeRaster(): string
    {
        if (self::$rupeeRasterCache !== null) {
            return self::$rupeeRasterCache;
        }

        $font = '/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf';
        if (!function_exists('imagecreatetruecolor') || !is_readable($font)) {
            self::$rupeeRasterCache = 'INR';

            return self::$rupeeRasterCache;
        }

        $w = 18;
        $h = 24;
        $im = imagecreatetruecolor($w, $h);
        $white = imagecolorallocate($im, 255, 255, 255);
        $black = imagecolorallocate($im, 0, 0, 0);
        imagefilledrectangle($im, 0, 0, $w, $h, $white);
        imagettftext($im, 14, 0, 0, 20, $black, $font, '₹');
        self::$rupeeRasterCache = $this->gdToRaster($im, false);
        imagedestroy($im);

        return self::$rupeeRasterCache;
    }

    protected function logoBlock(?string $logoUrl, int $maxWidthDots): string
    {
        if (!$logoUrl || !function_exists('imagecreatefromstring')) {
            return '';
        }
        try {
            $path = $this->resolveLocalImagePath($logoUrl);
            $bin  = $path ? @file_get_contents($path) : @file_get_contents($logoUrl);
            if ($bin === false || $bin === '') {
                return '';
            }
            $im = @imagecreatefromstring($bin);
            if (!$im) {
                return '';
            }
            $srcW = imagesx($im);
            $srcH = imagesy($im);
            if ($srcW < 1 || $srcH < 1) {
                imagedestroy($im);

                return '';
            }
            $maxWidthDots = max(64, min(512, $maxWidthDots));
            $maxWidthDots = (int) (floor($maxWidthDots / 8) * 8);
            $dstW = min($maxWidthDots, $srcW);
            $dstH = (int) max(1, round($srcH * ($dstW / $srcW)));
            $dst  = imagecreatetruecolor($dstW, $dstH);
            $white = imagecolorallocate($dst, 255, 255, 255);
            imagefilledrectangle($dst, 0, 0, $dstW, $dstH, $white);
            imagecopyresampled($dst, $im, 0, 0, 0, 0, $dstW, $dstH, $srcW, $srcH);
            imagedestroy($im);
            $raster = $this->gdToRaster($dst, true);
            imagedestroy($dst);

            return "\x1B\x61\x01" . $raster . "\x1B\x61\x00\n";
        } catch (\Throwable $e) {
            Log::info('ESC/POS logo skip: ' . $e->getMessage());

            return '';
        }
    }

    protected function resolveLocalImagePath(string $url): ?string
    {
        $path = parse_url($url, PHP_URL_PATH);
        if (!$path) {
            return null;
        }
        $local = public_path(ltrim($path, '/'));
        if (is_readable($local)) {
            return $local;
        }
        // Spatie media paths under storage
        if (str_contains($path, '/storage/')) {
            $storage = storage_path('app/public/' . ltrim(str_replace('/storage/', '', $path), '/'));
            if (is_readable($storage)) {
                return $storage;
            }
        }

        return null;
    }

    /**
     * GS v 0 raster from GD image (1-bit).
     */
    protected function gdToRaster($im, bool $centerPad = false): string
    {
        $width  = imagesx($im);
        $height = imagesy($im);
        $widthBytes = (int) ceil($width / 8);
        $data = '';
        for ($y = 0; $y < $height; $y++) {
            for ($xByte = 0; $xByte < $widthBytes; $xByte++) {
                $byte = 0;
                for ($bit = 0; $bit < 8; $bit++) {
                    $x = $xByte * 8 + $bit;
                    if ($x >= $width) {
                        continue;
                    }
                    $rgb = imagecolorat($im, $x, $y);
                    $r = ($rgb >> 16) & 0xFF;
                    $g = ($rgb >> 8) & 0xFF;
                    $b = $rgb & 0xFF;
                    $lum = (int) (0.299 * $r + 0.587 * $g + 0.114 * $b);
                    if ($lum < 160) {
                        $byte |= 0x80 >> $bit;
                    }
                }
                $data .= chr($byte);
            }
        }
        $xL = $widthBytes & 0xFF;
        $xH = ($widthBytes >> 8) & 0xFF;
        $yL = $height & 0xFF;
        $yH = ($height >> 8) & 0xFF;

        return "\x1D\x76\x30\x00" . chr($xL) . chr($xH) . chr($yL) . chr($yH) . $data;
    }

    /** Amount as ASCII "Rs.12.34" — mid-line bitmaps break thermal column alignment. */
    public function thermalMoney(mixed $value): string
    {
        return 'Rs.' . $this->moneyNumber($value);
    }

    public function moneyNumber(mixed $value): string
    {
        if (is_numeric($value)) {
            return number_format((float) $value, 2, '.', '');
        }
        $s = (string) $value;
        if (preg_match('/(\d[\d,]*\.?\d*)/', $s, $m)) {
            return number_format((float) str_replace(',', '', $m[1]), 2, '.', '');
        }

        return '0.00';
    }

    /** Legacy alias */
    public function sanitizeEscPosText(string $text): string
    {
        return $this->ascii($text);
    }

    public function sanitizeMoney(string $amount): string
    {
        return $this->thermalMoney($amount);
    }

    protected function ascii(string $text): string
    {
        $map = [
            '₹' => 'Rs.',
            '₨' => 'Rs.',
            'Гé╣' => 'Rs.',
            'â‚¹' => 'Rs.',
            '—' => '-',
            '–' => '-',
            '“' => '"',
            '”' => '"',
            '‘' => "'",
            '’' => "'",
            '…' => '...',
            '×' => 'x',
        ];
        $text = str_replace(array_keys($map), array_values($map), $text);
        if (function_exists('iconv')) {
            $converted = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text);
            if ($converted !== false) {
                $text = $converted;
            }
        }

        return preg_replace('/[^\x09\x0A\x0D\x20-\x7E]/', '?', $text) ?? $text;
    }

    protected function clipLine(string $line, int $width): string
    {
        // Approximate: RUPEE counts as 2 columns
        $vis = 0;
        $out = '';
        $len = strlen($line);
        for ($i = 0; $i < $len; $i++) {
            $ch = $line[$i];
            $add = ($ch === self::RUPEE) ? 2 : 1;
            if ($vis + $add > $width) {
                break;
            }
            $out .= $ch;
            $vis += $add;
        }

        return $out;
    }

    protected function visibleLen(string $text): int
    {
        return strlen($text) + substr_count($text, self::RUPEE);
    }

    protected function center(string $text, int $width): string
    {
        $text = trim($text);
        if ($text === '') {
            return '';
        }
        $len = $this->visibleLen($text);
        if ($len >= $width) {
            return $this->clipLine($text, $width);
        }
        $pad = (int) floor(($width - $len) / 2);

        return str_repeat(' ', $pad) . $text;
    }

    protected function pair(string $left, string $right, int $width): string
    {
        $left  = rtrim($left);
        $right = trim($right);
        $space = $width - $this->visibleLen($left) - $this->visibleLen($right);
        if ($space < 1) {
            // Truncate left to fit
            $maxLeft = max(4, $width - $this->visibleLen($right) - 1);
            $left    = $this->clipLine($left, $maxLeft);
            $space   = max(1, $width - $this->visibleLen($left) - $this->visibleLen($right));
        }

        return $left . str_repeat(' ', $space) . $right;
    }

    protected function wrapWords(string $text, int $width): array
    {
        $words = preg_split('/\s+/', trim($text)) ?: [];
        $rows  = [];
        $cur   = '';
        foreach ($words as $w) {
            $try = $cur === '' ? $w : ($cur . ' ' . $w);
            if ($this->visibleLen($try) <= $width) {
                $cur = $try;
            } else {
                if ($cur !== '') {
                    $rows[] = $cur;
                }
                $cur = $this->clipLine($w, $width);
            }
        }
        if ($cur !== '') {
            $rows[] = $cur;
        }

        return $rows;
    }
}
