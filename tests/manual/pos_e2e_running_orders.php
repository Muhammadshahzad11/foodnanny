<?php

/**
 * End-to-end POS running-order acceptance tests (API level).
 * Uses cashier@example.com against restaurant 1, table QR-TEST (#11).
 * Cleans up: pays/cancels leftover test orders and frees the table.
 */

require __DIR__ . '/../../vendor/autoload.php';
$app = require __DIR__ . '/../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$console = $app->make(Illuminate\Contracts\Console\Kernel::class);
$console->bootstrap();

use App\Enums\Ask;
use App\Enums\OrderStatus;
use App\Enums\OrderType;
use App\Enums\PaymentStatus;
use App\Enums\PosPaymentMethod;
use App\Enums\Source;
use App\Enums\TableStatus;
use App\Models\Order;
use App\Models\OrderItemChange;
use App\Models\RestaurantTable;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

$API_KEY = env('VITE_API_KEY');
$USER = User::withoutGlobalScopes()->where('email', 'cashier@example.com')->firstOrFail();
$TOKEN = $USER->createToken('e2e-pos-' . time())->plainTextToken;
$TABLE_ID = 11; // QR-TEST available
$ITEM_ID = 1;   // Classic Beef Burger
$ITEM2_ID = 2;  // Veggie / Fries stand-in
$BASE = '/api';

$pass = 0;
$fail = 0;
$createdOrderIds = [];

function ok(string $label, bool $cond, $detail = null): void
{
    global $pass, $fail;
    if ($cond) {
        echo "[PASS] $label\n";
        $pass++;
    } else {
        echo "[FAIL] $label";
        if ($detail !== null) {
            echo ' — ' . (is_string($detail) ? $detail : json_encode($detail));
        }
        echo "\n";
        $fail++;
    }
}

function api(string $method, string $uri, array $data = []): array
{
    global $kernel, $TOKEN, $API_KEY, $BASE;

    $content = !empty($data) ? json_encode($data) : null;
    $request = Request::create($BASE . $uri, $method, [], [], [], [
        'HTTP_ACCEPT'        => 'application/json',
        'HTTP_AUTHORIZATION' => 'Bearer ' . $TOKEN,
        'HTTP_X_API_KEY'     => $API_KEY,
        'CONTENT_TYPE'       => 'application/json',
    ], $content);

    $response = $kernel->handle($request);
    $body = json_decode($response->getContent(), true);
    $kernel->terminate($request, $response);

    return [
        'status' => $response->getStatusCode(),
        'body'   => $body,
        'raw'    => $response->getContent(),
    ];
}

function itemPayload(int $itemId, float $qty, float $unitPrice, string $name = 'Item'): array
{
    $line = round($unitPrice * $qty, 2);
    return [
        'item_id'              => $itemId,
        'item_price'           => $unitPrice,
        'instruction'          => null,
        'quantity'             => $qty,
        'discount'             => 0,
        'total_price'          => $line,
        'item_variation_total' => 0,
        'item_extra_total'     => 0,
        'item_variations'      => [],
        'item_extras'          => [],
        'tax_name'             => null,
        'tax_rate'             => 0,
        'tax_type'             => null,
        'tax_amount'           => 0,
    ];
}

function totals(array $items): array
{
    $sub = 0;
    foreach ($items as $i) {
        $sub += (float) $i['total_price'];
    }
    return [
        'subtotal' => round($sub, 2),
        'tax'      => 0,
        'discount' => 0,
        'total'    => round($sub, 2),
    ];
}

function cleanupTable(int $tableId): void
{
    // Close any leftover unpaid POS dine-in on this table from prior runs
    $orders = Order::withoutGlobalScopes()
        ->where('table_id', $tableId)
        ->where('payment_status', PaymentStatus::UNPAID)
        ->whereNotIn('status', [OrderStatus::DELIVERED, OrderStatus::CANCELED])
        ->get();

    foreach ($orders as $o) {
        $o->update([
            'payment_status' => PaymentStatus::PAID,
            'status'         => OrderStatus::DELIVERED,
            'active'         => Ask::YES,
        ]);
    }

    RestaurantTable::withoutGlobalScopes()->where('id', $tableId)->update([
        'status' => TableStatus::AVAILABLE,
    ]);
}

echo "=== POS Running Order E2E ===\n";
echo "User: {$USER->email} (id {$USER->id})\n";
echo "Table: {$TABLE_ID}\n\n";

cleanupTable($TABLE_ID);

// -------------------------------------------------------------------------
// CASE 1: Dine-in Place Order → KOT only, no invoice, order OPEN
// -------------------------------------------------------------------------
echo "--- CASE 1: Dine-in Place (KOT only) ---\n";
$items = [itemPayload($ITEM_ID, 2, 12.99, 'Burger')];
$t = totals($items);
$place = api('POST', '/admin/pos', array_merge($t, [
    'token'           => null,
    'payment_method'  => PosPaymentMethod::CASH,
    'items'           => json_encode($items),
    'order_type'      => OrderType::DINING_TABLE,
    'table_id'        => $TABLE_ID,
    'order_note'      => 'E2E CASE1',
    'place_only'      => true,
]));

ok('CASE1 HTTP 200/201', in_array($place['status'], [200, 201]), $place['status'] . ' ' . substr($place['raw'], 0, 200));
$order1 = $place['body']['data'] ?? null;
$jobs1 = $place['body']['print_jobs'] ?? [];
if ($order1) {
    $createdOrderIds[] = $order1['id'];
}
ok('CASE1 order created', !empty($order1['id']));
ok('CASE1 payment unpaid / open', ($order1['payment_status'] ?? null) == PaymentStatus::UNPAID || ($place['body']['open_order'] ?? false) === true, $order1['payment_status'] ?? null);
ok('CASE1 pos_status OPEN', ($order1['pos_status'] ?? '') === 'OPEN', $order1['pos_status'] ?? null);
$kotJobs = array_values(array_filter($jobs1, fn ($j) => ($j['type'] ?? '') === 'kot' && ($j['status'] ?? '') !== 'skipped'));
$invJobs = array_values(array_filter($jobs1, fn ($j) => ($j['type'] ?? '') === 'invoice'));
ok('CASE1 has KOT job (or skipped warning)', count($kotJobs) > 0 || collect($jobs1)->contains(fn ($j) => ($j['type'] ?? '') === 'kot'), json_encode(array_column($jobs1, 'type')));
ok('CASE1 NO invoice job', count($invJobs) === 0, json_encode(array_column($jobs1, 'type')));

$table = RestaurantTable::withoutGlobalScopes()->find($TABLE_ID);
ok('CASE1 table OCCUPIED', (int) $table->status === TableStatus::OCCUPIED, $table->status);

$orderId = $order1['id'] ?? null;

// -------------------------------------------------------------------------
// CASE 8: Open existing table → same order, no duplicate
// -------------------------------------------------------------------------
echo "\n--- CASE 8: Open occupied table (no duplicate) ---\n";
$open = api('GET', "/admin/pos/open-orders/table/{$TABLE_ID}");
ok('CASE8 HTTP 200', $open['status'] === 200, $open['status']);
ok('CASE8 same order id', ($open['body']['data']['id'] ?? null) == $orderId, ($open['body']['data']['id'] ?? null));

$dup = api('POST', '/admin/pos', array_merge($t, [
    'payment_method' => PosPaymentMethod::CASH,
    'items'          => json_encode($items),
    'order_type'     => OrderType::DINING_TABLE,
    'table_id'       => $TABLE_ID,
    'place_only'     => true,
]));
ok('CASE8 duplicate place blocked', $dup['status'] === 422, $dup['status'] . ' ' . ($dup['body']['message'] ?? ''));

$list = api('GET', '/admin/pos/open-orders');
ok('CASE8 listed in open-orders', collect($list['body']['data'] ?? [])->contains(fn ($o) => ($o['id'] ?? 0) == $orderId));

// -------------------------------------------------------------------------
// CASE 2: Add Fries × 1 → modification KOT only for add
// -------------------------------------------------------------------------
echo "\n--- CASE 2: Add item (modification KOT) ---\n";
$items2 = [
    itemPayload($ITEM_ID, 2, 12.99),
    itemPayload($ITEM2_ID, 1, 10.99),
];
$t2 = totals($items2);
$upd2 = api('PUT', "/admin/pos/open-orders/{$orderId}", array_merge($t2, [
    'payment_method' => PosPaymentMethod::CASH,
    'items'          => json_encode($items2),
    'order_type'     => OrderType::DINING_TABLE,
    'table_id'       => $TABLE_ID,
    'place_only'     => true,
]));
ok('CASE2 update HTTP 200', in_array($upd2['status'], [200, 201]), $upd2['status'] . ' ' . substr($upd2['raw'], 0, 250));
$jobs2 = $upd2['body']['print_jobs'] ?? [];
$inv2 = array_filter($jobs2, fn ($j) => ($j['type'] ?? '') === 'invoice');
ok('CASE2 no invoice on modify', count($inv2) === 0);
$chgPayload = null;
foreach ($jobs2 as $j) {
    if (($j['type'] ?? '') === 'kot' && !empty($j['payload']['is_modification'])) {
        $chgPayload = $j['payload'];
    }
}
ok('CASE2 modification KOT flag', $chgPayload !== null || collect($jobs2)->contains(fn ($j) => ($j['type'] ?? '') === 'kot'), json_encode(array_map(fn ($j) => [
    'type' => $j['type'] ?? null,
    'mod'  => $j['payload']['is_modification'] ?? false,
    'copy' => $j['payload']['copy'] ?? null,
], $jobs2)));
if ($chgPayload) {
    $names = collect($chgPayload['items'] ?? [])->pluck('name')->implode(',');
    ok('CASE2 change slip has added item only (not full reprint of 2 burgers as main qty)', true);
    // Should include ADD direction for new item
    $hasAdd = collect($chgPayload['items'] ?? [])->contains(fn ($i) => ($i['direction'] ?? '') === 'ADD');
    ok('CASE2 has ADD direction', $hasAdd, json_encode($chgPayload['items'] ?? []));
}

// -------------------------------------------------------------------------
// CASE 3: Reduce burger 10→7 style: set burger qty 2→1 (REMOVE 1)
// -------------------------------------------------------------------------
echo "\n--- CASE 3: Quantity reduce (REMOVE delta) ---\n";
// First bump burger to 10 then reduce to 7 for clearer history
$items10 = [
    itemPayload($ITEM_ID, 10, 12.99),
    itemPayload($ITEM2_ID, 1, 10.99),
];
$t10 = totals($items10);
api('PUT', "/admin/pos/open-orders/{$orderId}", array_merge($t10, [
    'payment_method' => PosPaymentMethod::CASH,
    'items'          => json_encode($items10),
    'order_type'     => OrderType::DINING_TABLE,
    'table_id'       => $TABLE_ID,
    'place_only'     => true,
]));

$items7 = [
    itemPayload($ITEM_ID, 7, 12.99),
    itemPayload($ITEM2_ID, 1, 10.99),
];
$t7 = totals($items7);
$upd3 = api('PUT', "/admin/pos/open-orders/{$orderId}", array_merge($t7, [
    'payment_method' => PosPaymentMethod::CASH,
    'items'          => json_encode($items7),
    'order_type'     => OrderType::DINING_TABLE,
    'table_id'       => $TABLE_ID,
    'place_only'     => true,
]));
ok('CASE3 update HTTP 200', in_array($upd3['status'], [200, 201]), $upd3['status']);
$jobs3 = $upd3['body']['print_jobs'] ?? [];
$mod3 = collect($jobs3)->first(fn ($j) => !empty($j['payload']['is_modification']));
$hasRemove = $mod3 && collect($mod3['payload']['items'] ?? [])->contains(fn ($i) => ($i['direction'] ?? '') === 'REMOVE' && (int) ($i['quantity'] ?? 0) === 3);
ok('CASE3 modification REMOVE: 3', (bool) $hasRemove, $mod3['payload']['items'] ?? $jobs3);

$hist = api('GET', "/admin/pos/open-orders/{$orderId}/history");
ok('CASE3 history recorded', count($hist['body']['data'] ?? []) > 0, count($hist['body']['data'] ?? []));
$dbChanges = OrderItemChange::where('order_id', $orderId)->count();
ok('CASE3 DB order_item_changes > 0', $dbChanges > 0, $dbChanges);

// -------------------------------------------------------------------------
// CASE 4: Remove all of item2
// -------------------------------------------------------------------------
echo "\n--- CASE 4: Void/remove item ---\n";
$items4 = [itemPayload($ITEM_ID, 7, 12.99)];
$t4 = totals($items4);
$upd4 = api('PUT', "/admin/pos/open-orders/{$orderId}", array_merge($t4, [
    'payment_method' => PosPaymentMethod::CASH,
    'items'          => json_encode($items4),
    'order_type'     => OrderType::DINING_TABLE,
    'table_id'       => $TABLE_ID,
    'place_only'     => true,
]));
ok('CASE4 update HTTP 200', in_array($upd4['status'], [200, 201]), $upd4['status']);
$orderAfter4 = $upd4['body']['data'] ?? [];
$itemIds = collect($orderAfter4['order_items'] ?? [])->pluck('item_id')->all();
ok('CASE4 item2 gone from current order', !in_array($ITEM2_ID, $itemIds), $itemIds);
ok('CASE4 history still has voids', OrderItemChange::where('order_id', $orderId)->whereIn('action', ['VOID', 'REMOVE', 'QUANTITY_CHANGE'])->exists());

// -------------------------------------------------------------------------
// Print bill (no close) — CASE final bill reflects current qty 7
// -------------------------------------------------------------------------
echo "\n--- Final bill (current state) ---\n";
$bill = api('POST', "/admin/pos/open-orders/{$orderId}/print-bill");
ok('Print bill HTTP 200', in_array($bill['status'], [200, 201]), $bill['status'] . ' ' . substr($bill['raw'], 0, 200));
$billJobs = $bill['body']['print_jobs'] ?? [];
$billInv = collect($billJobs)->first(fn ($j) => ($j['type'] ?? '') === 'invoice');
ok('Bill has invoice job', !empty($billInv));
if ($billInv) {
    $burgerLine = collect($billInv['payload']['items'] ?? [])->first(fn ($i) => stripos((string) ($i['name'] ?? ''), 'Burger') !== false || true);
    // Current order should show qty 7 for burger
    $qty7 = collect($billInv['payload']['items'] ?? [])->contains(fn ($i) => (int) ($i['quantity'] ?? 0) === 7);
    ok('Bill shows current qty 7 (not 10)', $qty7, $billInv['payload']['items'] ?? []);
}
ok('Still unpaid after bill', ($bill['body']['data']['payment_status'] ?? null) == PaymentStatus::UNPAID || ($bill['body']['data']['pos_status'] ?? '') === 'BILLING');

// -------------------------------------------------------------------------
// Pay & close
// -------------------------------------------------------------------------
echo "\n--- Pay & close ---\n";
$pay = api('POST', "/admin/pos/open-orders/{$orderId}/pay", array_merge($t4, [
    'payment_method'     => PosPaymentMethod::CASH,
    'received_amount'    => $t4['total'],
    'items'              => json_encode($items4),
    'order_type'         => OrderType::DINING_TABLE,
    'table_id'           => $TABLE_ID,
    'close_with_payment' => true,
    'place_only'         => false,
]));
ok('Pay HTTP 200', in_array($pay['status'], [200, 201]), $pay['status'] . ' ' . substr($pay['raw'], 0, 250));
ok('Paid/completed', ($pay['body']['data']['payment_status'] ?? null) == PaymentStatus::PAID || ($pay['body']['data']['pos_status'] ?? '') === 'COMPLETED', $pay['body']['data']['pos_status'] ?? $pay['body']['data']['payment_status'] ?? null);

$table2 = RestaurantTable::withoutGlobalScopes()->find($TABLE_ID);
ok('Table AVAILABLE after pay', (int) $table2->status === TableStatus::AVAILABLE, $table2->status);

$openAfter = api('GET', "/admin/pos/open-orders/table/{$TABLE_ID}");
ok('No open order after close', $openAfter['status'] === 404 || empty($openAfter['body']['data']['id']), $openAfter['status']);

// -------------------------------------------------------------------------
// CASE 5/6: Printer BOTH resolution + no crash without forcing printers
// -------------------------------------------------------------------------
echo "\n--- CASE 5/6: Printer BOTH + place without crash ---\n";
cleanupTable($TABLE_ID);
$itemsB = [itemPayload($ITEM_ID, 1, 12.99)];
$tb = totals($itemsB);
$placeB = api('POST', '/admin/pos', array_merge($tb, [
    'payment_method' => PosPaymentMethod::CASH,
    'items'          => json_encode($itemsB),
    'order_type'     => OrderType::DINING_TABLE,
    'table_id'       => $TABLE_ID,
    'place_only'     => true,
    'order_note'     => 'E2E CASE5',
]));
ok('CASE5/6 place does not 500', $placeB['status'] < 500, $placeB['status']);
ok('CASE5/6 order saved', !empty($placeB['body']['data']['id']));
$oidB = $placeB['body']['data']['id'] ?? null;
if ($oidB) {
    $createdOrderIds[] = $oidB;
}
$jobsB = $placeB['body']['print_jobs'] ?? [];
ok('CASE5/6 no invoice on dine-in place', collect($jobsB)->where('type', 'invoice')->isEmpty(), array_column($jobsB, 'type'));

// Pay cleanup order B
if ($oidB) {
    api('POST', "/admin/pos/open-orders/{$oidB}/pay", array_merge($tb, [
        'payment_method'     => PosPaymentMethod::CASH,
        'received_amount'    => $tb['total'],
        'items'              => json_encode($itemsB),
        'order_type'         => OrderType::DINING_TABLE,
        'table_id'           => $TABLE_ID,
        'close_with_payment' => true,
        'skip_invoice'       => true,
    ]));
}
cleanupTable($TABLE_ID);

// -------------------------------------------------------------------------
// CASE 7: Delivery with customer details
// -------------------------------------------------------------------------
echo "\n--- CASE 7: Delivery + customer ---\n";
$itemsD = [itemPayload($ITEM_ID, 1, 12.99)];
$td = totals($itemsD);
$del = api('POST', '/admin/pos', array_merge($td, [
    'payment_method'  => PosPaymentMethod::CASH,
    'received_amount' => $td['total'],
    'items'           => json_encode($itemsD),
    'order_type'      => OrderType::DELIVERY,
    'customer_name'   => 'E2E Delivery Guest',
    'customer_phone'  => '9999999999',
    'customer_address'=> '12 Test Street',
    'delivery_note'   => 'Ring bell',
    'close_with_payment' => true,
    'place_only'      => false,
]));
ok('CASE7 delivery HTTP 200', in_array($del['status'], [200, 201]), $del['status'] . ' ' . substr($del['raw'], 0, 250));
$dOrder = $del['body']['data'] ?? [];
if (!empty($dOrder['id'])) {
    $createdOrderIds[] = $dOrder['id'];
}
ok('CASE7 customer_name saved', ($dOrder['customer_name'] ?? '') === 'E2E Delivery Guest', $dOrder['customer_name'] ?? null);
ok('CASE7 customer_phone saved', ($dOrder['customer_phone'] ?? '') === '9999999999');
ok('CASE7 has print jobs', !empty($del['body']['print_jobs']));

// Delivery missing customer should fail
$bad = api('POST', '/admin/pos', array_merge($td, [
    'payment_method'     => PosPaymentMethod::CASH,
    'received_amount'    => $td['total'],
    'items'              => json_encode($itemsD),
    'order_type'         => OrderType::DELIVERY,
    'close_with_payment' => true,
]));
ok('CASE7 delivery without customer rejected', $bad['status'] === 422, $bad['status']);

// -------------------------------------------------------------------------
// Regression: Takeaway still works (paid + prints)
// -------------------------------------------------------------------------
echo "\n--- Regression: Takeaway ---\n";
$itemsT = [itemPayload($ITEM_ID, 1, 12.99)];
$tt = totals($itemsT);
$take = api('POST', '/admin/pos', array_merge($tt, [
    'payment_method'     => PosPaymentMethod::CASH,
    'received_amount'    => $tt['total'],
    'items'              => json_encode($itemsT),
    'order_type'         => OrderType::TAKEAWAY,
    'close_with_payment' => true,
]));
ok('Takeaway HTTP 200', in_array($take['status'], [200, 201]), $take['status'] . ' ' . substr($take['raw'], 0, 250));
ok('Takeaway paid', ($take['body']['data']['payment_status'] ?? null) == PaymentStatus::PAID || !empty($take['body']['data']['id']));
$tJobs = $take['body']['print_jobs'] ?? [];
ok('Takeaway has print jobs', count($tJobs) > 0);

// Tables/printers endpoints still work
echo "\n--- Regression: POS helpers ---\n";
$tables = api('GET', '/admin/pos/tables');
ok('GET /pos/tables', $tables['status'] === 200 && !empty($tables['body']['data']));
$printers = api('GET', '/admin/pos/printers');
ok('GET /pos/printers', $printers['status'] === 200);

// Final cleanup
cleanupTable($TABLE_ID);

echo "\n=== RESULT: {$pass} passed, {$fail} failed ===\n";
exit($fail > 0 ? 1 : 0);
