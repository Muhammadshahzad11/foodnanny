<?php

namespace Database\Seeders;

use App\Enums\Ask;
use App\Enums\IsAdvance;
use App\Enums\KitchenItemStatus;
use App\Enums\KitchenPriority;
use App\Enums\OrderStatus;
use App\Enums\OrderType;
use App\Enums\PaymentGateway;
use App\Enums\PaymentStatus;
use App\Enums\Role as EnumRole;
use App\Enums\Source;
use App\Enums\Status;
use App\Enums\TableStatus;
use App\Models\DefaultAccess;
use App\Models\Item;
use App\Models\KitchenStation;
use App\Models\KitchenStatusLog;
use App\Models\KitchenTicket;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Restaurant;
use App\Models\RestaurantTable;
use App\Models\User;
use App\Services\RestaurantTableQrService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Seeds Modules 1–6 test data:
 * Employees, Tables + QR, Waiter orders, Kitchen board orders/tickets.
 *
 * Idempotent — safe to re-run:
 *   php artisan db:seed --class=ModuleTestDataSeeder
 *
 * Test logins (password: 123456)
 *   admin@example.com
 *   owner@example.com
 *   waiter@example.com
 *   chef@example.com
 *   cashier@example.com
 *   manager@example.com
 */
class ModuleTestDataSeeder extends Seeder
{
    protected const PASSWORD = '123456';

    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $restaurant = $this->ensureRestaurant();
        $this->ensureAdmin();
        $items      = $this->ensureItems($restaurant);
        $staff      = $this->ensureStaff($restaurant);
        $stations   = $this->ensureStations($restaurant);
        $tables     = $this->ensureTables($restaurant);
        $this->generateQrCodes($tables);
        $this->seedKitchenBoard($restaurant, $staff, $tables, $items, $stations);
        $this->seedWaiterDrafts($restaurant, $staff, $tables, $items);
        $this->seedPosOrder($restaurant, $items, $stations, $staff);

        $this->command?->info('Module test data ready.');
        $this->command?->table(
            ['Account', 'Password', 'Use for'],
            [
                ['admin@example.com', self::PASSWORD, 'Admin monitor'],
                ['owner@example.com', self::PASSWORD, 'Restaurant owner'],
                ['waiter@example.com', self::PASSWORD, 'Waiter ordering'],
                ['chef@example.com', self::PASSWORD, 'Kitchen display'],
                ['cashier@example.com', self::PASSWORD, 'Cashier'],
                ['manager@example.com', self::PASSWORD, 'Manager'],
            ]
        );
    }

    protected function ensureRestaurant(): Restaurant
    {
        $restaurant = Restaurant::withoutGlobalScopes()->first();

        if (!$restaurant) {
            $restaurant = Restaurant::withoutGlobalScopes()->create([
                'name'         => 'Cost to Cost Foods',
                'slug'         => 'cost-to-cost-foods',
                'email'        => 'kitchen@costtocostfoods.test',
                'phone'        => '+1-555-0100',
                'country_code' => '+1',
                'status'       => Status::ACTIVE,
                'creator_type' => User::class,
                'creator_id'   => 1,
                'editor_type'  => User::class,
                'editor_id'    => 1,
            ]);
        } else {
            $restaurant->update([
                'name'   => $restaurant->name ?: 'Cost to Cost Foods',
                'status' => Status::ACTIVE,
            ]);
        }

        return $restaurant->fresh();
    }

    protected function ensureAdmin(): void
    {
        $user = User::withTrashed()->where('email', 'admin@example.com')->first();
        if ($user && $user->trashed()) {
            $user->restore();
        }

        if (!$user) {
            $user = User::create([
                'name'                 => 'John Doe',
                'email'                => 'admin@example.com',
                'phone'                => '1728660901',
                'username'             => 'admin',
                'email_verified_at'    => now(),
                'password'             => Hash::make(self::PASSWORD),
                'restaurant_id'        => 0,
                'balance'              => 0,
                'collection'           => 0,
                'status'               => Status::ACTIVE,
                'country_code'         => '+1',
                'is_guest'             => Ask::NO,
                'terms_and_conditions' => Ask::YES,
                'creator_type'         => User::class,
                'creator_id'           => 1,
                'editor_type'          => User::class,
                'editor_id'            => 1,
            ]);
        } else {
            $user->forceFill([
                'password' => Hash::make(self::PASSWORD),
                'status'   => Status::ACTIVE,
            ])->save();
        }

        $role = Role::query()->find(EnumRole::ADMIN);
        if ($role) {
            $user->syncRoles([$role]);
        }
    }

    protected function ensureItems(Restaurant $restaurant)
    {
        $items = Item::withoutGlobalScopes()
            ->where('restaurant_id', $restaurant->id)
            ->orderBy('id')
            ->get()
            ->values();

        if ($items->isEmpty()) {
            $categoryId = DB::table('item_categories')->where('restaurant_id', $restaurant->id)->value('id')
                ?? DB::table('item_categories')->value('id')
                ?? 1;

            $seed = [
                ['Classic Beef Burger', 12.99],
                ['Veggie Burger', 10.99],
                ['Chicken Burger', 11.99],
                ['Margherita Pizza', 14.50],
                ['Pepperoni Pizza', 15.50],
                ['Large Coke', 3.50],
                ['Garden Salad', 7.25],
                ['Loaded Fries', 6.50],
            ];

            foreach ($seed as $index => [$name, $price]) {
                Item::withoutGlobalScopes()->create([
                    'name'             => $name,
                    'slug'             => Str::slug($name) . '-' . $restaurant->id . '-' . ($index + 1),
                    'restaurant_id'    => $restaurant->id,
                    'item_category_id' => $categoryId,
                    'price'            => $price,
                    'status'           => Status::ACTIVE,
                    'item_type'        => 5,
                    'order'            => $index + 1,
                    'is_halal'         => Ask::NO,
                    'creator_type'     => User::class,
                    'creator_id'       => 1,
                    'editor_type'      => User::class,
                    'editor_id'        => 1,
                ]);
            }

            $items = Item::withoutGlobalScopes()
                ->where('restaurant_id', $restaurant->id)
                ->orderBy('id')
                ->get()
                ->values();
        }

        return $items;
    }

    protected function ensureStaff(Restaurant $restaurant): array
    {
        $defs = [
            'owner' => [
                'email' => 'owner@example.com',
                'name'  => 'Test Owner',
                'phone' => '1700000010',
                'role'  => EnumRole::RESTAURANT_OWNER,
                'user'  => 'test-owner',
            ],
            'waiter' => [
                'email' => 'waiter@example.com',
                'name'  => 'Test Waiter',
                'phone' => '1700000011',
                'role'  => EnumRole::WAITER,
                'user'  => 'test-waiter',
            ],
            'chef' => [
                'email' => 'chef@example.com',
                'name'  => 'Test Chef',
                'phone' => '1700000012',
                'role'  => EnumRole::CHEF,
                'user'  => 'test-chef',
            ],
            'cashier' => [
                'email' => 'cashier@example.com',
                'name'  => 'Test Cashier',
                'phone' => '1700000013',
                'role'  => EnumRole::CASHIER,
                'user'  => 'test-cashier',
            ],
            'manager' => [
                'email' => 'manager@example.com',
                'name'  => 'Test Manager',
                'phone' => '1700000014',
                'role'  => EnumRole::MANAGER,
                'user'  => 'test-manager',
            ],
        ];

        $staff = [];
        foreach ($defs as $key => $def) {
            $user = User::withTrashed()->where('email', $def['email'])->first();
            if ($user && $user->trashed()) {
                $user->restore();
            }

            if (!$user) {
                $user = User::create([
                    'name'                 => $def['name'],
                    'email'                => $def['email'],
                    'phone'                => $def['phone'],
                    'username'             => $def['user'],
                    'email_verified_at'    => now(),
                    'password'             => Hash::make(self::PASSWORD),
                    'restaurant_id'        => $restaurant->id,
                    'balance'              => 0,
                    'collection'           => 0,
                    'status'               => Status::ACTIVE,
                    'country_code'         => '+1',
                    'is_guest'             => Ask::NO,
                    'terms_and_conditions' => Ask::YES,
                    'creator_type'         => User::class,
                    'creator_id'           => 1,
                    'editor_type'          => User::class,
                    'editor_id'            => 1,
                ]);
            } else {
                $user->forceFill([
                    'name'          => $def['name'],
                    'password'      => Hash::make(self::PASSWORD),
                    'restaurant_id' => $restaurant->id,
                    'status'        => Status::ACTIVE,
                ])->save();
            }

            $role = Role::query()->find($def['role']);
            if ($role) {
                $user->syncRoles([$role]);
            }

            DefaultAccess::query()->updateOrCreate(
                ['user_id' => $user->id, 'name' => 'restaurant_id'],
                ['default_id' => $restaurant->id]
            );

            $staff[$key] = $user->fresh();
        }

        return $staff;
    }

    protected function ensureStations(Restaurant $restaurant): array
    {
        $defs = [
            ['Default', 'default', 1],
            ['Hot Line', 'hot', 2],
            ['Cold Line', 'cold', 3],
            ['Drinks', 'drinks', 4],
        ];

        $stations = [];
        foreach ($defs as [$name, $code, $sort]) {
            $stations[$code] = KitchenStation::query()->firstOrCreate(
                [
                    'restaurant_id' => $restaurant->id,
                    'code'          => $code,
                ],
                [
                    'name'       => $name,
                    'sort_order' => $sort,
                    'status'     => Status::ACTIVE,
                ]
            );
        }

        return $stations;
    }

    protected function ensureTables(Restaurant $restaurant)
    {
        $defs = [
            ['T1', 'Window Table', 2, 'Main Hall', TableStatus::AVAILABLE],
            ['T2', 'Family Table', 6, 'Main Hall', TableStatus::AVAILABLE],
            ['T3', 'Corner Booth', 4, 'Main Hall', TableStatus::OCCUPIED],
            ['A1', 'VIP Booth', 4, 'VIP', TableStatus::OCCUPIED],
            ['A2', 'Patio 1', 2, 'Patio', TableStatus::AVAILABLE],
            ['A3', 'Patio 2', 4, 'Patio', TableStatus::RESERVED],
            ['B1', 'Bar Counter 1', 2, 'Bar', TableStatus::AVAILABLE],
            ['B2', 'Bar Counter 2', 2, 'Bar', TableStatus::CLEANING],
            ['C1', 'Private Room', 8, 'Private', TableStatus::AVAILABLE],
            ['X1', 'Out of Service', 4, 'Main Hall', TableStatus::OUT_OF_SERVICE],
            ['QR-TEST', 'QR Demo Table', 4, 'Main Hall', TableStatus::AVAILABLE],
            ['W5', 'Waiter Demo', 4, 'Main Hall', TableStatus::AVAILABLE],
        ];

        $tables = collect();
        foreach ($defs as [$number, $name, $capacity, $zone, $status]) {
            $table = RestaurantTable::withoutGlobalScopes()
                ->withTrashed()
                ->where('restaurant_id', $restaurant->id)
                ->where('table_number', $number)
                ->first();

            if ($table && $table->trashed()) {
                $table->restore();
            }

            if (!$table) {
                $table = RestaurantTable::withoutGlobalScopes()->create([
                    'restaurant_id' => $restaurant->id,
                    'table_number'  => $number,
                    'name'          => $name,
                    'capacity'      => $capacity,
                    'zone'          => $zone,
                    'status'        => $status,
                    'notes'         => 'Module test table',
                    'creator_type'  => User::class,
                    'creator_id'    => 1,
                    'editor_type'   => User::class,
                    'editor_id'     => 1,
                ]);
            } else {
                $table->update([
                    'name'     => $name,
                    'capacity' => $capacity,
                    'zone'     => $zone,
                    'status'   => $status,
                ]);
            }

            $tables->push($table->fresh());
        }

        return $tables->keyBy('table_number');
    }

    protected function generateQrCodes($tables): void
    {
        /** @var RestaurantTableQrService $qr */
        $qr = app(RestaurantTableQrService::class);

        foreach ($tables as $table) {
            try {
                if (blank($table->qr_token)) {
                    $qr->generate($table, true);
                }
            } catch (\Throwable $e) {
                // Fallback without media binary if QR lib fails in some envs
                if (blank($table->qr_token)) {
                    $token = bin2hex(random_bytes(32));
                    $table->update([
                        'qr_token'        => $token,
                        'qr_version'      => 1,
                        'qr_generated_at' => now(),
                        'qr_url'          => url('/t/' . $token),
                    ]);
                }
            }
        }
    }

    protected function seedKitchenBoard(
        Restaurant $restaurant,
        array $staff,
        $tables,
        $items,
        array $stations
    ): void {
        // Clear previous module demo KOTs tagged in order_note prefix to stay idempotent-ish
        $this->purgeDemoOrders($restaurant->id, 'DEMO-KDS');

        $chef   = $staff['chef'];
        $waiter = $staff['waiter'];
        $now    = Carbon::now();

        $scenarios = [
            [
                'tag'      => 'Pending claim',
                'table'    => 'T3',
                'status'   => OrderStatus::ACCEPT,
                'priority' => KitchenPriority::HIGH,
                'note'     => 'DEMO-KDS · Allergy: peanuts',
                'minutes'  => 3,
                'items'    => [
                    [$items[0] ?? null, 2, 'No onion', [['name' => 'Large']], [['name' => 'Extra Cheese']]],
                    [$items[5] ?? $items[0] ?? null, 2, 'Extra ice', [], []],
                ],
                'item_status' => KitchenItemStatus::PENDING,
            ],
            [
                'tag'      => 'Accepted',
                'table'    => 'A1',
                'status'   => OrderStatus::ACCEPT,
                'priority' => KitchenPriority::VIP,
                'note'     => 'DEMO-KDS · VIP guest — rush',
                'minutes'  => 8,
                'accepted' => true,
                'items'    => [
                    [$items[3] ?? null, 1, 'Well done crust', [['name' => 'Large']], [['name' => 'Extra Cheese']]],
                    [$items[6] ?? null, 1, 'Dressing on side', [], []],
                ],
                'item_status' => KitchenItemStatus::PENDING,
            ],
            [
                'tag'      => 'Preparing',
                'table'    => 'T2',
                'status'   => OrderStatus::PREPARING,
                'priority' => KitchenPriority::URGENT,
                'note'     => 'DEMO-KDS · Less spicy',
                'minutes'  => 15,
                'accepted' => true,
                'preparing'=> true,
                'items'    => [
                    [$items[2] ?? null, 3, 'No mayo', [['name' => 'Combo']], [['name' => 'Bacon']]],
                    [$items[7] ?? null, 2, null, [], [['name' => 'Cheese']]],
                ],
                'item_status' => KitchenItemStatus::PREPARING,
            ],
            [
                'tag'      => 'Ready',
                'table'    => 'B1',
                'status'   => OrderStatus::PREPARED,
                'priority' => KitchenPriority::NORMAL,
                'note'     => 'DEMO-KDS · Ready for waiter pickup',
                'minutes'  => 22,
                'accepted' => true,
                'preparing'=> true,
                'ready'    => true,
                'items'    => [
                    [$items[1] ?? null, 1, 'Gluten-free bun', [], []],
                    [$items[5] ?? null, 1, null, [['name' => 'Large']], []],
                ],
                'item_status' => KitchenItemStatus::READY,
                'ticket'      => true,
            ],
            [
                'tag'      => 'Completed',
                'table'    => 'A2',
                'status'   => OrderStatus::DELIVERED,
                'priority' => KitchenPriority::NORMAL,
                'note'     => 'DEMO-KDS · Served earlier today',
                'minutes'  => 55,
                'accepted' => true,
                'preparing'=> true,
                'ready'    => true,
                'items'    => [
                    [$items[4] ?? null, 1, null, [['name' => 'Medium']], []],
                ],
                'item_status' => KitchenItemStatus::SERVED,
                'ticket'      => true,
            ],
            [
                'tag'      => 'Cancelled',
                'table'    => 'C1',
                'status'   => OrderStatus::CANCELED,
                'priority' => KitchenPriority::NORMAL,
                'note'     => 'DEMO-KDS · Guest left',
                'minutes'  => 40,
                'accepted' => true,
                'reason'   => 'Customer cancelled',
                'items'    => [
                    [$items[0] ?? null, 1, null, [], []],
                ],
                'item_status' => KitchenItemStatus::PENDING,
            ],
            [
                'tag'      => 'Rejected',
                'table'    => 'A3',
                'status'   => OrderStatus::REJECTED,
                'priority' => KitchenPriority::HIGH,
                'note'     => 'DEMO-KDS · Out of stock',
                'minutes'  => 12,
                'accepted' => true,
                'reason'   => 'Item unavailable',
                'items'    => [
                    [$items[3] ?? null, 2, null, [], []],
                ],
                'item_status' => KitchenItemStatus::PENDING,
            ],
        ];

        foreach ($scenarios as $scenario) {
            $table = $tables[$scenario['table']] ?? $tables->first();
            $order = $this->createOrder([
                'restaurant_id'    => $restaurant->id,
                'table_id'         => $table?->id,
                'waiter_id'        => $waiter->id,
                'user_id'          => 2,
                'status'           => $scenario['status'],
                'active'           => Ask::YES,
                'order_type'       => OrderType::DINING_TABLE,
                'source'           => Source::WAITER,
                'order_note'       => $scenario['note'],
                'reason'           => $scenario['reason'] ?? null,
                'kitchen_priority' => $scenario['priority'],
                'kitchen_station_id' => $stations['hot']->id ?? null,
                'order_datetime'   => $now->copy()->subMinutes($scenario['minutes']),
                'preparation_time' => 20,
            ], $chef, $scenario);

            $this->attachItems($order, $scenario['items'], $scenario['item_status'], $stations['hot']->id ?? null);
            $this->logKitchen($order, $chef, $scenario);

            if (!empty($scenario['ticket'])) {
                $this->createTicket($order, $chef);
            }

            if ($table && in_array($scenario['status'], [
                OrderStatus::ACCEPT,
                OrderStatus::PREPARING,
                OrderStatus::PREPARED,
            ], true)) {
                $table->update(['status' => TableStatus::OCCUPIED]);
            }
        }
    }

    protected function seedWaiterDrafts(Restaurant $restaurant, array $staff, $tables, $items): void
    {
        $this->purgeDemoOrders($restaurant->id, 'DEMO-WAITER');

        $waiter = $staff['waiter'];
        $table  = $tables['W5'] ?? $tables->first();

        $draft = $this->createOrder([
            'restaurant_id'    => $restaurant->id,
            'table_id'         => $table?->id,
            'waiter_id'        => $waiter->id,
            'user_id'          => 2,
            'status'           => OrderStatus::PENDING,
            'active'           => Ask::NO,
            'order_type'       => OrderType::DINING_TABLE,
            'source'           => Source::WAITER,
            'order_note'       => 'DEMO-WAITER · Draft — not sent to kitchen yet',
            'kitchen_priority' => KitchenPriority::NORMAL,
            'order_datetime'   => now()->subMinutes(2),
            'preparation_time' => 20,
        ], $waiter, []);

        $this->attachItems($draft, [
            [$items[0] ?? null, 1, 'Hold the pickles', [], [['name' => 'Extra Cheese']]],
            [$items[5] ?? null, 1, null, [], []],
        ], KitchenItemStatus::PENDING, null);

        if ($table) {
            $table->update(['status' => TableStatus::AVAILABLE]);
        }
    }

    protected function seedPosOrder(Restaurant $restaurant, $items, array $stations, array $staff): void
    {
        $this->purgeDemoOrders($restaurant->id, 'DEMO-POS');

        $order = $this->createOrder([
            'restaurant_id'      => $restaurant->id,
            'table_id'           => null,
            'waiter_id'          => null,
            'user_id'            => 2,
            'status'             => OrderStatus::ACCEPT,
            'active'             => Ask::YES,
            'order_type'         => OrderType::POS,
            'source'             => Source::POS,
            'order_note'         => 'DEMO-POS · Counter takeaway for kitchen',
            'kitchen_priority'   => KitchenPriority::HIGH,
            'kitchen_station_id' => $stations['hot']->id ?? null,
            'order_datetime'     => now()->subMinutes(5),
            'preparation_time'   => 15,
            'payment_status'     => PaymentStatus::PAID,
        ], $staff['cashier'], []);

        $this->attachItems($order, [
            [$items[0] ?? null, 1, 'To go', [['name' => 'Regular']], []],
            [$items[7] ?? null, 1, null, [], []],
        ], KitchenItemStatus::PENDING, $stations['hot']->id ?? null);
    }

    protected function purgeDemoOrders(int $restaurantId, string $prefix): void
    {
        $ids = Order::withoutGlobalScopes()
            ->where('restaurant_id', $restaurantId)
            ->where('order_note', 'like', $prefix . '%')
            ->pluck('id');

        if ($ids->isEmpty()) {
            return;
        }

        KitchenTicket::query()->whereIn('order_id', $ids)->delete();
        KitchenStatusLog::query()->whereIn('order_id', $ids)->delete();
        OrderItem::withoutGlobalScopes()->whereIn('order_id', $ids)->delete();
        Order::withoutGlobalScopes()->whereIn('id', $ids)->delete();
    }

    protected function createOrder(array $attrs, User $actor, array $scenario): Order
    {
        $subtotal = 0;
        // totals filled after items; provisional
        $order = Order::withoutGlobalScopes()->create([
            'user_id'            => $attrs['user_id'],
            'restaurant_id'      => $attrs['restaurant_id'],
            'table_id'           => $attrs['table_id'] ?? null,
            'waiter_id'          => $attrs['waiter_id'] ?? null,
            'subtotal'           => 0,
            'discount'           => 0,
            'delivery_fee'       => 0,
            'total_tax'          => 0,
            'total'              => 0,
            'order_type'         => $attrs['order_type'],
            'order_datetime'     => $attrs['order_datetime'],
            'delivery_time'      => now()->format('H:i') . ' - ' . now()->addMinutes(30)->format('H:i'),
            'preparation_time'   => $attrs['preparation_time'] ?? 20,
            'is_advance_order'   => IsAdvance::NO,
            'payment_method'     => PaymentGateway::CASH_ON_DELIVERY,
            'payment_status'     => $attrs['payment_status'] ?? PaymentStatus::UNPAID,
            'status'             => $attrs['status'],
            'reason'             => $attrs['reason'] ?? null,
            'order_note'         => $attrs['order_note'] ?? null,
            'kitchen_priority'   => $attrs['kitchen_priority'] ?? KitchenPriority::NORMAL,
            'kitchen_station_id' => $attrs['kitchen_station_id'] ?? null,
            'kitchen_accepted_by'=> !empty($scenario['accepted']) ? $actor->id : null,
            'kitchen_accepted_at'=> !empty($scenario['accepted']) ? Carbon::parse($attrs['order_datetime'])->addMinutes(1) : null,
            'kitchen_preparing_by'=> !empty($scenario['preparing']) ? $actor->id : null,
            'kitchen_ready_by'   => !empty($scenario['ready']) ? $actor->id : null,
            'source'             => $attrs['source'],
            'active'             => $attrs['active'],
            'creator_type'       => User::class,
            'creator_id'         => $actor->id,
            'editor_type'        => User::class,
            'editor_id'          => $actor->id,
        ]);

        $order->order_serial_no = date('dmy') . $order->id;
        $order->save();

        return $order->fresh();
    }

    protected function attachItems(Order $order, array $lines, int $kitchenStatus, ?int $stationId): void
    {
        $subtotal = 0;

        foreach ($lines as $line) {
            [$item, $qty, $instruction, $variations, $extras] = array_pad($line, 5, null);
            if (!$item) {
                continue;
            }

            $qty   = max(1, (int) $qty);
            $price = (float) ($item->price ?? 10);
            $total = $price * $qty;
            $subtotal += $total;

            OrderItem::withoutGlobalScopes()->create([
                'order_id'             => $order->id,
                'restaurant_id'        => $order->restaurant_id,
                'item_id'              => $item->id,
                'quantity'             => $qty,
                'discount'             => 0,
                'tax_name'             => 'VAT',
                'tax_rate'             => 0,
                'tax_type'             => 1,
                'tax_amount'           => 0,
                'price'                => $price,
                'item_variations'      => json_encode($variations ?: []),
                'item_extras'          => json_encode($extras ?: []),
                'instruction'          => $instruction,
                'item_variation_total' => 0,
                'item_extra_total'     => 0,
                'total_price'          => $total,
                'status'               => Status::ACTIVE,
                'kitchen_status'       => $kitchenStatus,
                'kitchen_station_id'   => $stationId,
            ]);
        }

        $order->update([
            'subtotal' => $subtotal,
            'total'    => $subtotal,
        ]);
    }

    protected function logKitchen(Order $order, User $chef, array $scenario): void
    {
        $from = OrderStatus::PENDING;
        $steps = [];

        if (!empty($scenario['accepted'])) {
            $steps[] = [OrderStatus::ACCEPT, 'accept'];
        }
        if (!empty($scenario['preparing'])) {
            $steps[] = [OrderStatus::PREPARING, 'preparing'];
        }
        if (!empty($scenario['ready'])) {
            $steps[] = [OrderStatus::PREPARED, 'ready'];
        }
        if ((int) $order->status === OrderStatus::CANCELED) {
            $steps[] = [OrderStatus::CANCELED, 'cancel'];
        }
        if ((int) $order->status === OrderStatus::REJECTED) {
            $steps[] = [OrderStatus::REJECTED, 'reject'];
        }
        if ((int) $order->status === OrderStatus::DELIVERED) {
            $steps[] = [OrderStatus::DELIVERED, 'complete'];
        }

        foreach ($steps as [$to, $action]) {
            KitchenStatusLog::create([
                'restaurant_id' => $order->restaurant_id,
                'order_id'      => $order->id,
                'from_status'   => $from,
                'to_status'     => $to,
                'user_id'       => $chef->id,
                'action'        => $action,
                'meta'          => ['seeded' => true, 'tag' => $scenario['tag'] ?? null],
            ]);
            $from = $to;
        }
    }

    protected function createTicket(Order $order, User $chef): void
    {
        KitchenTicket::create([
            'restaurant_id' => $order->restaurant_id,
            'order_id'      => $order->id,
            'ticket_no'     => 'KOT-' . $order->order_serial_no,
            'print_count'   => 1,
            'printed_at'    => now()->subMinutes(5),
            'printed_by'    => $chef->id,
            'payload'       => [
                'copy'            => 'KITCHEN',
                'order_serial_no' => $order->order_serial_no,
                'order_note'      => $order->order_note,
                'seeded'          => true,
            ],
        ]);
    }
}
