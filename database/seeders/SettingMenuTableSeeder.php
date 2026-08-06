<?php

namespace Database\Seeders;


use App\Models\SettingMenu;
use App\Enums\SettingMenuType;
use Illuminate\Database\Seeder;

class SettingMenuTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $menus = [
            [
                'name'       => 'Company',
                'language'   => 'company',
                'url'        => 'company',
                'icon'       => 'lab lab-line-company',
                'type'       => SettingMenuType::SYSTEM,
                'priority'   => 1000,
                'status'     => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name'       => 'Site',
                'language'   => 'site',
                'url'        => 'site',
                'icon'       => 'lab lab-line-site',
                'type'       => SettingMenuType::SYSTEM,
                'priority'   => 995,
                'status'     => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name'       => 'Frontend',
                'language'   => 'frontend',
                'url'        => 'frontend',
                'icon'       => 'lab lab-line-frontend',
                'type'       => SettingMenuType::SYSTEM,
                'priority'   => 990,
                'status'     => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name'       => 'Terms & Conditions',
                'language'   => 'terms_and_conditions',
                'url'        => 'terms-and-conditions',
                'icon'       => 'lab lab-line-terms-and-conditions',
                'type'       => SettingMenuType::SYSTEM,
                'priority'   => 985,
                'status'     => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name'       => 'Mail',
                'language'   => 'mail',
                'url'        => 'mail',
                'icon'       => 'lab lab-line-mail',
                'type'       => SettingMenuType::SYSTEM,
                'priority'   => 980,
                'status'     => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name'       => 'Pusher',
                'language'   => 'pusher',
                'url'        => 'pusher',
                'icon'       => 'lab lab-line-pusher',
                'type'       => SettingMenuType::SYSTEM,
                'priority'   => 975,
                'status'     => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name'       => 'Storage',
                'language'   => 'storage',
                'url'        => 'storage',
                'icon'       => 'lab lab-line-storage',
                'type'       => SettingMenuType::SYSTEM,
                'priority'   => 970,
                'status'     => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name'       => 'Cache',
                'language'   => 'cache',
                'url'        => 'cache',
                'icon'       => 'lab lab-line-reset',
                'type'       => SettingMenuType::SYSTEM,
                'priority'   => 968,
                'status'     => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name'       => 'Delivery Setup',
                'language'   => 'delivery_setup',
                'url'        => 'delivery-setup',
                'icon'       => 'lab lab-line-delivery-setup',
                'type'       => SettingMenuType::SYSTEM,
                'priority'   => 965,
                'status'     => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name'       => 'OTP',
                'language'   => 'otp',
                'url'        => 'otp',
                'icon'       => 'lab lab-line-otp',
                'type'       => SettingMenuType::SYSTEM,
                'priority'   => 960,
                'status'     => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name'       => 'Notification',
                'language'   => 'notification',
                'url'        => 'notification',
                'icon'       => 'lab lab-line-notification',
                'type'       => SettingMenuType::SYSTEM,
                'priority'   => 955,
                'status'     => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name'       => 'Notification Alert',
                'language'   => 'notification_alert',
                'url'        => 'notification-alert',
                'icon'       => 'lab lab-line-notification-alert',
                'type'       => SettingMenuType::SYSTEM,
                'priority'   => 950,
                'status'     => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name'       => 'Social Media',
                'language'   => 'social_media',
                'url'        => 'social-media',
                'icon'       => 'lab lab-line-social',
                'type'       => SettingMenuType::SYSTEM,
                'priority'   => 945,
                'status'     => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name'       => 'Cookies',
                'language'   => 'cookies',
                'url'        => 'cookies',
                'icon'       => 'lab lab-line-cookies',
                'type'       => SettingMenuType::SYSTEM,
                'priority'   => 940,
                'status'     => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name'       => 'Analytics',
                'language'   => 'analytics',
                'url'        => 'analytics',
                'icon'       => 'lab lab-line-analytic',
                'type'       => SettingMenuType::SYSTEM,
                'priority'   => 935,
                'status'     => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name'       => 'Theme',
                'language'   => 'theme',
                'url'        => 'theme',
                'icon'       => 'lab lab-line-theme',
                'type'       => SettingMenuType::SYSTEM,
                'priority'   => 930,
                'status'     => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name'       => 'Currencies',
                'language'   => 'currencies',
                'url'        => 'currencies',
                'icon'       => 'lab lab-line-currencies',
                'type'       => SettingMenuType::SYSTEM,
                'priority'   => 925,
                'status'     => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name'       => 'Rider Tips',
                'language'   => 'rider_tips',
                'url'        => 'rider-tips',
                'icon'       => 'lab lab-line-raider-tip',
                'type'       => SettingMenuType::SYSTEM,
                'priority'   => 920,
                'status'     => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name'       => 'About Steps',
                'language'   => 'about_steps',
                'url'        => 'about-steps',
                'icon'       => 'lab lab-line-about-steps',
                'type'       => SettingMenuType::SYSTEM,
                'priority'   => 910,
                'status'     => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name'       => 'Benefits',
                'language'   => 'benefits',
                'url'        => 'benefits',
                'icon'       => 'lab lab-line-benefits',
                'type'       => SettingMenuType::SYSTEM,
                'priority'   => 905,
                'status'     => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name'       => 'Pages',
                'language'   => 'pages',
                'url'        => 'pages',
                'icon'       => 'lab lab-line-pages',
                'type'       => SettingMenuType::SYSTEM,
                'priority'   => 900,
                'status'     => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name'       => 'Role Permissions',
                'language'   => 'role_permissions',
                'url'        => 'role',
                'icon'       => 'lab lab-line-role-permission',
                'type'       => SettingMenuType::SYSTEM,
                'priority'   => 895,
                'status'     => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name'       => 'Languages',
                'language'   => 'languages',
                'url'        => 'languages',
                'icon'       => 'lab-line-language',
                'type'       => SettingMenuType::SYSTEM,
                'priority'   => 890,
                'status'     => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name'       => 'AI Agent',
                'language'   => 'ai_agent',
                'url'        => 'ai-agent',
                'icon'       => 'lab lab-line-ai-agent',
                'type'       => SettingMenuType::SYSTEM,
                'priority'   => 885,
                'status'     => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name'       => 'SMS Gateway',
                'language'   => 'sms_gateway',
                'url'        => 'sms-gateway',
                'icon'       => 'lab lab-line-sms',
                'type'       => SettingMenuType::SYSTEM,
                'priority'   => 880,
                'status'     => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name'       => 'Payment Gateway',
                'language'   => 'payment_gateway',
                'url'        => 'payment-gateway',
                'icon'       => 'lab lab-line-payment-gateway',
                'type'       => SettingMenuType::SYSTEM,
                'priority'   => 875,
                'status'     => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name'       => 'Progressive Web App',
                'language'   => 'progressive_web_app',
                'url'        => 'pwa',
                'icon'       => 'lab lab-line-monitor-mobile',
                'type'       => SettingMenuType::SYSTEM,
                'priority'   => 870,
                'status'     => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name'       => 'My Restaurant',
                'language'   => 'my_restaurant',
                'url'        => 'my-restaurant',
                'icon'       => 'lab lab-line-restaurants',
                'type'       => SettingMenuType::RESTAURANT,
                'priority'   => 865,
                'status'     => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name'       => 'Order Setup',
                'language'   => 'order_setup',
                'url'        => 'order-setup',
                'icon'       => 'lab lab-line-order-setup',
                'type'       => SettingMenuType::RESTAURANT,
                'priority'   => 860,
                'status'     => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name'       => 'Time Slots',
                'language'   => 'time_slots',
                'url'        => 'time-slots',
                'icon'       => 'lab-line-time-slots',
                'type'       => SettingMenuType::RESTAURANT,
                'priority'   => 855,
                'status'     => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name'       => 'Item Categories',
                'language'   => 'item_categories',
                'url'        => 'item-categories',
                'icon'       => 'lab lab-line-item-categories',
                'type'       => SettingMenuType::RESTAURANT,
                'priority'   => 850,
                'status'     => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name'       => 'Item Attributes',
                'language'   => 'item_attributes',
                'url'        => 'item-attributes',
                'icon'       => 'lab lab-line-item-attributes',
                'type'       => SettingMenuType::RESTAURANT,
                'priority'   => 845,
                'status'     => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name'       => 'Taxes',
                'language'   => 'taxes',
                'url'        => 'taxes',
                'icon'       => 'lab lab-line-taxes',
                'type'       => SettingMenuType::RESTAURANT,
                'priority'   => 840,
                'status'     => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name'       => 'Delivery Location Setup',
                'language'   => 'delivery_location_setup',
                'url'        => 'delivery-location-setup',
                'icon'       => 'lab lab-line-delivery-location-setup',
                'type'       => SettingMenuType::DELIVERY_BOY,
                'priority'   => 835,
                'status'     => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        SettingMenu::insert($menus);
    }
}
