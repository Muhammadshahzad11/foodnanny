<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class MenuTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Parent IDs must come from real inserted rows. Never assume auto-increment
     * starts at 1 — migrations may have inserted Kitchen/Waiter/Tables first.
     */
    public function run(): void
    {
        $menus = [
            [
                'name'       => 'Dashboard',
                'language'   => 'dashboard',
                'url'        => 'dashboard',
                'icon'       => 'lab lab-line-dashboard',
                'priority'   => 20,
                'status'     => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name'       => 'Restaurant Operations',
                'language'   => 'restaurant_operations',
                'url'        => '#',
                'icon'       => 'lab lab-line-restaurants',
                'priority'   => 21,
                'status'     => 1,
                'created_at' => now(),
                'updated_at' => now(),
                'children'   => [
                    [
                        'name'       => 'Tables',
                        'language'   => 'tables',
                        'url'        => 'tables',
                        'icon'       => 'lab lab-line-restaurants',
                        'priority'   => 22,
                        'status'     => 1,
                        'created_at' => now(),
                        'updated_at' => now()
                    ],
                    [
                        'name'       => 'Waiter',
                        'language'   => 'waiter',
                        'url'        => 'waiter',
                        'icon'       => 'lab lab-line-users',
                        'priority'   => 23,
                        'status'     => 1,
                        'created_at' => now(),
                        'updated_at' => now()
                    ],
                    [
                        'name'       => 'Kitchen',
                        'language'   => 'kitchen',
                        'url'        => 'kitchen',
                        'icon'       => 'lab lab-line-flame',
                        'priority'   => 24,
                        'status'     => 1,
                        'created_at' => now(),
                        'updated_at' => now()
                    ],
                ]
            ],
            [
                'name'       => 'Restaurants',
                'language'   => 'restaurants',
                'url'        => 'restaurants',
                'icon'       => 'lab lab-line-restaurants',
                'priority'   => 30,
                'status'     => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name'       => 'Cuisines',
                'language'   => 'cuisines',
                'url'        => 'cuisines',
                'icon'       => 'lab lab-line-cuisine',
                'priority'   => 31,
                'status'     => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name'       => 'Items',
                'language'   => 'items',
                'url'        => 'items',
                'icon'       => 'lab lab-items',
                'priority'   => 40,
                'status'     => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name'       => 'Order History',
                'language'   => 'order_history',
                'url'        => '#',
                'icon'       => 'lab line-items',
                'priority'   => 100,
                'status'     => 1,
                'created_at' => now(),
                'updated_at' => now(),
                'children'   => [
                    [
                        'name'       => 'Order Tracker',
                        'language'   => 'order_tracker',
                        'url'        => 'order-tracker',
                        'icon'       => 'lab lab-line-order-tracker',
                        'priority'   => 100,
                        'status'     => 1,
                        'created_at' => now(),
                        'updated_at' => now()
                    ],
                    [
                        'name'       => 'Return Orders',
                        'language'   => 'return_orders',
                        'url'        => 'return-orders',
                        'icon'       => 'lab lab-line-return-order',
                        'priority'   => 100,
                        'status'     => 1,
                        'created_at' => now(),
                        'updated_at' => now()
                    ],
                    [
                        'name'       => 'Refunds',
                        'language'   => 'refunds',
                        'url'        => 'refunds',
                        'icon'       => 'lab lab-line-refund',
                        'priority'   => 100,
                        'status'     => 1,
                        'created_at' => now(),
                        'updated_at' => now()
                    ],
                    [
                        'name'       => 'Reviews',
                        'language'   => 'reviews',
                        'url'        => 'reviews',
                        'icon'       => 'lab lab-line-review',
                        'priority'   => 100,
                        'status'     => 1,
                        'created_at' => now(),
                        'updated_at' => now()
                    ],
                ]
            ],
            [
                'name'       => 'Users',
                'language'   => 'users',
                'url'        => '#',
                'icon'       => 'lab ',
                'priority'   => 100,
                'status'     => 1,
                'created_at' => now(),
                'updated_at' => now(),
                'children'   => [
                    [
                        'name'       => 'Administrators',
                        'language'   => 'administrators',
                        'url'        => 'administrators',
                        'icon'       => 'lab lab-line-administrators',
                        'priority'   => 100,
                        'status'     => 1,
                        'created_at' => now(),
                        'updated_at' => now()
                    ],
                    [
                        'name'       => 'Delivery Boys',
                        'language'   => 'delivery_boys',
                        'url'        => 'delivery-boys',
                        'icon'       => 'lab lab-line-delivery-boys',
                        'priority'   => 100,
                        'status'     => 1,
                        'created_at' => now(),
                        'updated_at' => now()
                    ],
                    [
                        'name'       => 'Customers',
                        'language'   => 'customers',
                        'url'        => 'customers',
                        'icon'       => 'lab lab-line-customers',
                        'priority'   => 100,
                        'status'     => 1,
                        'created_at' => now(),
                        'updated_at' => now()
                    ],
                    [
                        'name'       => 'Employees',
                        'language'   => 'employees',
                        'url'        => 'employees',
                        'icon'       => 'lab lab-line-employees',
                        'priority'   => 100,
                        'status'     => 1,
                        'created_at' => now(),
                        'updated_at' => now()
                    ],
                    [
                        'name'       => 'Restaurant Owners',
                        'language'   => 'restaurant_owners',
                        'url'        => 'restaurant-owners',
                        'icon'       => 'lab lab-line-restaurant-owners',
                        'priority'   => 100,
                        'status'     => 1,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]
                ]
            ],
            [
                'name'       => 'Orders',
                'language'   => 'orders',
                'url'        => '#',
                'icon'       => 'lab lab-pos',
                'priority'   => 100,
                'status'     => 1,
                'created_at' => now(),
                'updated_at' => now(),
                'children'   => [
                    [
                        'name'       => 'Available Orders',
                        'language'   => 'available_orders',
                        'url'        => 'available-orders',
                        'icon'       => 'lab lab-line-available-orders',
                        'priority'   => 100,
                        'status'     => 1,
                        'created_at' => now(),
                        'updated_at' => now()

                    ],
                    [
                        'name'       => 'Active Orders',
                        'language'   => 'active_orders',
                        'url'        => 'active-orders',
                        'icon'       => 'lab lab-line-active-orders',
                        'priority'   => 100,
                        'status'     => 1,
                        'created_at' => now(),
                        'updated_at' => now()

                    ]
                ]
            ],
            [
                'name'       => 'Pos & Orders',
                'language'   => 'pos_and_orders',
                'url'        => '#',
                'icon'       => 'lab lab-pos',
                'priority'   => 100,
                'status'     => 1,
                'created_at' => now(),
                'updated_at' => now(),
                'children'   => [
                    [
                        'name'       => 'POS',
                        'url'        => 'pos',
                        'language'   => 'pos',
                        'icon'       => 'lab lab-line-pos',
                        'priority'   => 100,
                        'status'     => 1,
                        'created_at' => now(),
                        'updated_at' => now()

                    ],
                    [
                        'name'       => 'POS Orders',
                        'language'   => 'pos_orders',
                        'url'        => 'pos-orders',
                        'icon'       => 'lab lab-line-pos-orders',
                        'priority'   => 100,
                        'status'     => 1,
                        'created_at' => now(),
                        'updated_at' => now()
                    ],
                    [
                        'name'       => 'Online Orders',
                        'language'   => 'online_orders',
                        'url'        => 'online-orders',
                        'icon'       => 'lab lab-online-orders',
                        'priority'   => 100,
                        'status'     => 1,
                        'created_at' => now(),
                        'updated_at' => now()

                    ]
                ]
            ],
            [
                'name'       => 'Accounts',
                'language'   => 'accounts',
                'url'        => '#',
                'icon'       => 'lab ',
                'priority'   => 100,
                'status'     => 1,
                'created_at' => now(),
                'updated_at' => now(),
                'children'   => [
                    [
                        'name'       => 'Transactions',
                        'language'   => 'transactions',
                        'url'        => 'transactions',
                        'icon'       => 'lab lab-line-transactions',
                        'priority'   => 100,
                        'status'     => 1,
                        'created_at' => now(),
                        'updated_at' => now()
                    ],
                    [
                        'name'       => 'Payouts',
                        'language'   => 'payouts',
                        'url'        => 'payouts',
                        'icon'       => 'lab lab-line-payout',
                        'priority'   => 100,
                        'status'     => 1,
                        'created_at' => now(),
                        'updated_at' => now()
                    ],
                    [
                        'name'       => 'Collections',
                        'language'   => 'collections',
                        'url'        => 'collections',
                        'icon'       => 'lab lab-line-collection',
                        'priority'   => 100,
                        'status'     => 1,
                        'created_at' => now(),
                        'updated_at' => now()
                    ],
                    [
                        'name'       => 'Cashouts',
                        'language'   => 'cashouts',
                        'url'        => 'cashouts',
                        'icon'       => 'lab lab-line-cashout',
                        'priority'   => 100,
                        'status'     => 1,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]
                ]
            ],
            [
                'name'       => 'Promo',
                'language'   => 'promo',
                'url'        => '#',
                'icon'       => 'lab ',
                'priority'   => 100,
                'status'     => 1,
                'created_at' => now(),
                'updated_at' => now(),
                'children'   => [
                    [
                        'name'       => 'Vouchers',
                        'language'   => 'vouchers',
                        'url'        => 'vouchers',
                        'icon'       => 'lab lab-line-coupon',
                        'priority'   => 100,
                        'status'     => 1,
                        'created_at' => now(),
                        'updated_at' => now()

                    ],
                    [
                        'name'       => 'Coupons',
                        'language'   => 'coupons',
                        'url'        => 'coupons',
                        'icon'       => 'lab lab-line-coupon',
                        'priority'   => 100,
                        'status'     => 1,
                        'created_at' => now(),
                        'updated_at' => now()

                    ],
                    [
                        'name'       => 'Offers',
                        'language'   => 'offers',
                        'url'        => 'offers',
                        'icon'       => 'lab lab-line-offers',
                        'priority'   => 100,
                        'status'     => 1,
                        'created_at' => now(),
                        'updated_at' => now()
                    ],
                    [
                        'name'       => 'Campaigns',
                        'language'   => 'campaigns',
                        'url'        => 'campaigns',
                        'icon'       => 'lab lab-line-campaign',
                        'priority'   => 100,
                        'status'     => 1,
                        'created_at' => now(),
                        'updated_at' => now()
                    ],
                    [
                        'name'       => 'Campaigns And Offers',
                        'language'   => 'campaigns_and_offers',
                        'url'        => 'campaigns-and-offers',
                        'icon'       => 'lab lab-line-campaign',
                        'priority'   => 100,
                        'status'     => 1,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]
                ]
            ],
            [
                'name'       => 'Communications',
                'language'   => 'communications',
                'url'        => '#',
                'icon'       => 'lab ',
                'priority'   => 100,
                'status'     => 1,
                'created_at' => now(),
                'updated_at' => now(),
                'children'   => [
                    [
                        'name'       => 'Push Notifications',
                        'language'   => 'push_notifications',
                        'url'        => 'push-notifications',
                        'icon'       => 'lab lab-line-notification-alert',
                        'priority'   => 100,
                        'status'     => 1,
                        'created_at' => now(),
                        'updated_at' => now()

                    ],
                    [
                        'name'       => 'Messages',
                        'language'   => 'messages',
                        'url'        => 'messages',
                        'icon'       => 'lab lab-line-message',
                        'priority'   => 100,
                        'status'     => 1,
                        'created_at' => now(),
                        'updated_at' => now()

                    ],
                    [
                        'name'       => 'Subscribers',
                        'language'   => 'subscribers',
                        'url'        => 'subscribers',
                        'icon'       => 'lab lab-line-social',
                        'priority'   => 100,
                        'status'     => 1,
                        'created_at' => now(),
                        'updated_at' => now()

                    ]
                ]
            ],
            [
                'name'       => 'Reports',
                'language'   => 'reports',
                'url'        => '#',
                'icon'       => 'lab ',
                'priority'   => 100,
                'status'     => 1,
                'created_at' => now(),
                'updated_at' => now(),
                'children'   => [
                    [
                        'name'       => 'Sales Report',
                        'language'   => 'sales_report',
                        'url'        => 'sales-report',
                        'icon'       => 'lab lab-line-sales-report',
                        'priority'   => 100,
                        'status'     => 1,
                        'created_at' => now(),
                        'updated_at' => now()

                    ],
                    [
                        'name'       => 'Items Report',
                        'language'   => 'items_report',
                        'url'        => 'items-report',
                        'icon'       => 'lab lab-line-items-report',
                        'priority'   => 100,
                        'status'     => 1,
                        'created_at' => now(),
                        'updated_at' => now()
                    ],
                    [
                        'name'       => 'Credit Balance Report',
                        'language'   => 'credit_balance_report',
                        'url'        => 'credit-balance-report',
                        'icon'       => 'lab lab-line-credit-balance-report',
                        'priority'   => 100,
                        'status'     => 1,
                        'created_at' => now(),
                        'updated_at' => now()
                    ],
                    [
                        'name'       => 'Collection Report',
                        'language'   => 'collection_report',
                        'url'        => 'collection-report',
                        'icon'       => 'lab lab-line-collection-report',
                        'priority'   => 100,
                        'status'     => 1,
                        'created_at' => now(),
                        'updated_at' => now()

                    ],
                ]
            ],
            [
                'name'       => 'Setup',
                'language'   => 'setup',
                'url'        => '#',
                'icon'       => 'lab ',
                'priority'   => 100,
                'status'     => 1,
                'created_at' => now(),
                'updated_at' => now(),
                'children'   => [
                    [
                        'name'       => 'Restaurant Settings',
                        'language'   => 'restaurant_settings',
                        'url'        => 'restaurant-settings',
                        'icon'       => 'lab lab-line-restaurant-setting',
                        'priority'   => 100,
                        'status'     => 1,
                        'created_at' => now(),
                        'updated_at' => now()
                    ],
                    [
                        'name'       => 'System Settings',
                        'language'   => 'system_settings',
                        'url'        => 'system-settings',
                        'icon'       => 'lab lab-line-system-settings',
                        'priority'   => 100,
                        'status'     => 1,
                        'created_at' => now(),
                        'updated_at' => now()
                    ],
                    [
                        'name'       => 'Delivery Boy Settings',
                        'language'   => 'delivery_boy_settings',
                        'url'        => 'delivery-boy-settings',
                        'icon'       => 'lab lab-line-delivery-boy-setting',
                        'priority'   => 100,
                        'status'     => 1,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]
                ]
            ],
        ];

        if (Menu::query()->exists()) {
            Menu::query()->delete();
            if (DB::getDriverName() !== 'sqlite') {
                DB::statement('ALTER TABLE menus AUTO_INCREMENT = 1');
            }
        }

        foreach ($menus as $menu) {
            $children = $menu['children'] ?? null;
            unset($menu['children']);
            $menu['parent'] = 0;
            $parentId       = Menu::query()->insertGetId($menu);

            if ($children) {
                foreach ($children as $child) {
                    $child['parent'] = $parentId;
                    Menu::query()->insert($child);
                }
            }
        }

        $this->ensureModuleMenus();
    }

    /**
     * Ensure Restaurant Operations parent + Tables / Waiter / Kitchen children
     * exist when menus were created by older migrations/seeds.
     */
    private function ensureModuleMenus(): void
    {
        $now = now();

        $parentId = Menu::query()
            ->where('url', '#')
            ->whereIn('language', ['restaurant_operations', 'operations'])
            ->value('id');

        if (!$parentId) {
            $parentId = Menu::query()->insertGetId([
                'name'       => 'Restaurant Operations',
                'language'   => 'restaurant_operations',
                'url'        => '#',
                'icon'       => 'lab lab-line-restaurants',
                'priority'   => 21,
                'status'     => 1,
                'parent'     => 0,
                'type'       => 1,
                'addon'      => 10,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        } else {
            Menu::query()->where('id', $parentId)->update([
                'name'       => 'Restaurant Operations',
                'language'   => 'restaurant_operations',
                'icon'       => 'lab lab-line-restaurants',
                'priority'   => 21,
                'updated_at' => $now,
            ]);
        }

        $children = [
            [
                'name'     => 'Tables',
                'language' => 'tables',
                'url'      => 'tables',
                'icon'     => 'lab lab-line-restaurants',
                'priority' => 22,
            ],
            [
                'name'     => 'Waiter',
                'language' => 'waiter',
                'url'      => 'waiter',
                'icon'     => 'lab lab-line-users',
                'priority' => 23,
            ],
            [
                'name'     => 'Kitchen',
                'language' => 'kitchen',
                'url'      => 'kitchen',
                'icon'     => 'lab lab-line-flame',
                'priority' => 24,
            ],
        ];

        foreach ($children as $child) {
            $existing = Menu::query()->where('url', $child['url'])->first();
            if ($existing) {
                $existing->update([
                    'parent'     => $parentId,
                    'priority'   => $child['priority'],
                    'icon'       => $child['icon'],
                    'language'   => $child['language'],
                    'name'       => $child['name'],
                    'updated_at' => $now,
                ]);
            } else {
                Menu::query()->insert([
                    'name'       => $child['name'],
                    'language'   => $child['language'],
                    'url'        => $child['url'],
                    'icon'       => $child['icon'],
                    'priority'   => $child['priority'],
                    'status'     => 1,
                    'parent'     => $parentId,
                    'type'       => 1,
                    'addon'      => 10,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }
}
