<?php

namespace Database\Seeders;

use App\Enums\Ask;
use App\Enums\OrderStatus;
use App\Enums\OrderType;
use App\Enums\PaymentStatus;
use App\Enums\Source;
use App\Enums\Status;
use App\Enums\TaxType;
use App\Models\Item;
use App\Models\Order;
use App\Models\OrderAddress;
use App\Models\OrderItem;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Database\Seeder;

class RiderAvailableOrderSeeder extends Seeder
{
    public function run(): void
    {
        $customer = User::query()->where('email', 'zone.customer@example.com')->first();
        if (!$customer) {
            $this->command?->error('Run ZoneDemoDataSeeder first (zone.customer@example.com missing).');
            return;
        }

        Order::withoutGlobalScopes()
            ->where('order_serial_no', 'like', 'RIDER-TEST-%')
            ->get()
            ->each(function (Order $order) {
                OrderItem::query()->where('order_id', $order->id)->delete();
                OrderAddress::query()->where('order_id', $order->id)->delete();
                $order->delete();
            });

        $jobs = [
            // 6 Madhapur — visible to Ravi / Ajay (zone 2, pin 17.4483,78.3915)
            ['zone_id' => 2, 'restaurant_id' => 2, 'lat' => '17.448300', 'lng' => '78.391500', 'label' => 'Home', 'address' => 'Madhapur, Hyderabad – rider test 1', 'status' => OrderStatus::PREPARING],
            ['zone_id' => 2, 'restaurant_id' => 4, 'lat' => '17.449100', 'lng' => '78.392200', 'label' => 'Home', 'address' => 'Madhapur, Hyderabad – rider test 2', 'status' => OrderStatus::PREPARED],
            ['zone_id' => 2, 'restaurant_id' => 5, 'lat' => '17.447400', 'lng' => '78.390100', 'label' => 'Work', 'address' => 'Madhapur, Hyderabad – rider test 3', 'status' => OrderStatus::PREPARING],
            ['zone_id' => 2, 'restaurant_id' => 6, 'lat' => '17.450200', 'lng' => '78.393000', 'label' => 'Home', 'address' => 'Madhapur, Hyderabad – rider test 4', 'status' => OrderStatus::PREPARED],
            ['zone_id' => 2, 'restaurant_id' => 1, 'lat' => '17.446800', 'lng' => '78.389400', 'label' => 'Home', 'address' => 'Madhapur, Hyderabad – rider test 5', 'status' => OrderStatus::PREPARING],
            ['zone_id' => 2, 'restaurant_id' => 2, 'lat' => '17.448900', 'lng' => '78.394100', 'label' => 'Other', 'address' => 'Madhapur, Hyderabad – rider test 6', 'status' => OrderStatus::PREPARED],
            // 4 Kondapur — visible to Rahul / Suresh (zone 4, pin 17.4700,78.3580)
            ['zone_id' => 4, 'restaurant_id' => 10, 'lat' => '17.470000', 'lng' => '78.358000', 'label' => 'Home', 'address' => 'Kondapur, Hyderabad – rider test 7', 'status' => OrderStatus::PREPARING],
            ['zone_id' => 4, 'restaurant_id' => 11, 'lat' => '17.471200', 'lng' => '78.359100', 'label' => 'Home', 'address' => 'Kondapur, Hyderabad – rider test 8', 'status' => OrderStatus::PREPARED],
            ['zone_id' => 4, 'restaurant_id' => 10, 'lat' => '17.468800', 'lng' => '78.356900', 'label' => 'Work', 'address' => 'Kondapur, Hyderabad – rider test 9', 'status' => OrderStatus::PREPARING],
            ['zone_id' => 4, 'restaurant_id' => 11, 'lat' => '17.472000', 'lng' => '78.357400', 'label' => 'Home', 'address' => 'Kondapur, Hyderabad – rider test 10', 'status' => OrderStatus::PREPARED],
        ];

        foreach ($jobs as $i => $job) {
            $serial = sprintf('RIDER-TEST-%02d', $i + 1);
            $restaurant = Restaurant::withoutGlobalScopes()->find($job['restaurant_id']);
            if (!$restaurant) {
                continue;
            }

            $item = Item::withoutGlobalScopes()->where('restaurant_id', $restaurant->id)->orderBy('id')->first();
            $price = $item ? (float) ($item->price ?? 199) : 199;
            $qty = 1;
            $tax = round($price * 0.05, 6);
            $fee = (int) $job['zone_id'] === 2 ? 30 : 25;
            $subtotal = $price * $qty;
            $total = $subtotal + $tax + $fee;

            $order = Order::withoutGlobalScopes()->create([
                'order_serial_no'      => $serial,
                'user_id'              => $customer->id,
                'restaurant_id'        => $restaurant->id,
                'zone_id'              => $job['zone_id'],
                'subtotal'             => $subtotal,
                'discount'             => 0,
                'delivery_fee'         => $fee,
                'total_tax'            => $tax,
                'total'                => $total,
                'order_type'           => OrderType::DELIVERY,
                'order_datetime'       => now()->subMinutes(10 - $i),
                'delivery_time'        => now()->addMinutes(30)->format('H:i') . ' - ' . now()->addMinutes(45)->format('H:i'),
                'preparation_time'     => 30,
                'is_advance_order'     => Ask::NO,
                'payment_method'       => 1,
                'payment_status'       => PaymentStatus::PAID,
                'status'               => $job['status'],
                'delivery_boy_id'      => null,
                'is_received'          => Ask::NO,
                'delivery_boy_request' => Ask::YES,
                'service_fee'          => 0,
                'customer_name'        => $customer->name,
                'customer_phone'       => $customer->phone,
                'customer_address'     => $job['address'],
                'source'               => Source::APP,
                'active'               => Status::ACTIVE,
            ]);

            OrderAddress::query()->create([
                'restaurant_id' => $restaurant->id,
                'order_id'      => $order->id,
                'user_id'       => $customer->id,
                'label'         => $job['label'],
                'address'       => $job['address'],
                'apartment'     => 'T-' . ($i + 1),
                'latitude'      => $job['lat'],
                'longitude'     => $job['lng'],
            ]);

            if ($item) {
                OrderItem::query()->create([
                    'order_id'              => $order->id,
                    'restaurant_id'         => $restaurant->id,
                    'item_id'               => $item->id,
                    'quantity'              => $qty,
                    'discount'              => 0,
                    'tax_name'              => 'VAT',
                    'tax_rate'              => 5,
                    'tax_type'              => TaxType::PERCENTAGE,
                    'tax_amount'            => $tax,
                    'price'                 => $price,
                    'item_variations'       => '[]',
                    'item_extras'           => '[]',
                    'item_variation_total'  => 0,
                    'item_extra_total'      => 0,
                    'total_price'           => $subtotal,
                    'instruction'           => '',
                    'status'                => Status::ACTIVE,
                ]);
            }
        }

        $this->command?->info('10 available rider orders created (RIDER-TEST-01 … 10).');
        $this->command?->info('Madhapur (6): ravi.rider@example.com / 123456');
        $this->command?->info('Kondapur (4): rahul.rider@example.com / 123456');
    }
}
