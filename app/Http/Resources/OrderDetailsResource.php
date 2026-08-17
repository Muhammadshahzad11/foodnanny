<?php

namespace App\Http\Resources;

use App\Enums\Ask;
use App\Enums\OrderStatus;
use App\Enums\OrderType;
use App\Enums\PaymentGateway;
use App\Enums\Role as EnumRole;
use App\Libraries\AppLibrary;
use App\Services\DeliveryOtpService;
use Carbon\Carbon;
use Dipokhalder\Settings\Facades\Settings;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class OrderDetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id'                          => $this->id,
            'order_serial_no'             => $this->order_serial_no,
            'token'                       => $this->token,
            'discount'                    => AppLibrary::convertAmountFormat($this->discount),
            'service_fee'                 => AppLibrary::convertAmountFormat($this->service_fee),
            'rider_tip'                   => AppLibrary::convertAmountFormat($this->rider_tip),
            'delivery_fee'                => AppLibrary::convertAmountFormat($this->delivery_fee),
            'total_tax'                   => AppLibrary::convertAmountFormat($this->total_tax),
            'subtotal'                    => AppLibrary::convertAmountFormat($this->subtotal),
            'total'                       => AppLibrary::convertAmountFormat($this->total),
            'subtotal_currency_price'     => AppLibrary::currencyAmountFormat($this->subtotal),
            'discount_currency_price'     => AppLibrary::currencyAmountFormat($this->discount),
            'delivery_fee_currency_price' => AppLibrary::currencyAmountFormat($this->delivery_fee),
            'rider_tip_currency_price'    => AppLibrary::currencyAmountFormat($this->rider_tip),
            'service_fee_currency_price'  => AppLibrary::currencyAmountFormat($this->service_fee),
            'total_currency_price'        => AppLibrary::currencyAmountFormat($this->total),
            'total_tax_currency_price'    => AppLibrary::currencyAmountFormat($this->total_tax),
            'order_type'                  => $this->order_type,
            'table_id'                    => $this->table_id,
            'waiter_id'                   => $this->waiter_id,
            'order_note'                  => $this->order_note,
            'customer_name'               => $this->customer_name,
            'customer_phone'              => $this->customer_phone,
            'customer_address'            => $this->customer_address,
            'delivery_note'               => $this->delivery_note,
            'billing_requested_at'        => optional($this->billing_requested_at)?->toIso8601String(),
            'pos_status'                  => $this->posStatusLabel(),
            'items_count'                 => $this->when(isset($this->order_items_count), $this->order_items_count, fn () => $this->orderItems?->count()),
            'table'                       => $this->when((int) $this->table_id > 0, function () {
                return [
                    'id'           => $this->diningTable?->id,
                    'name'         => $this->diningTable?->name,
                    'table_number' => $this->diningTable?->table_number,
                    'zone'         => $this->diningTable?->zone,
                ];
            }),
            'waiter'                      => $this->when((int) $this->waiter_id > 0, function () {
                return [
                    'id'   => $this->waiter?->id,
                    'name' => $this->waiter?->name,
                ];
            }),
            'order_datetime'              => AppLibrary::datetime($this->order_datetime),
            'order_datetime_iso'          => optional($this->order_datetime)?->toIso8601String(),
            'order_date'                  => AppLibrary::date($this->order_datetime),
            'order_time'                  => AppLibrary::time($this->order_datetime),
            'delivery_date'               => $this->is_advance_order == Ask::YES ? AppLibrary::increaseDate($this->order_datetime, 1) : AppLibrary::date($this->order_datetime),
            'delivery_time'               => AppLibrary::deliveryTime($this->delivery_time),
            'payment_method'              => $this->payment_method,
            'payment_method_label'        => $this->paymentMethodLabel(),
            'payment_status'              => $this->payment_status,
            'is_advance_order'            => $this->is_advance_order,
            'preparation_time'            => $this->preparation_time,
            'status'                      => $this->status,
            'cutlery'                     => $this->cutlery,
            'is_received'                 => $this->is_received,
            'status_name'                 => trans('order_status.' . $this->status),
            'reason'                      => $this->reason,
            'is_scan_menu_order'          => $this->resource->isScanMenuOrder(),
            'cancel_window_seconds'       => $this->resource->customerCanCancel() ? $this->resource->cancelWindowSeconds() : 0,
            'cancel_expires_at'           => $this->resource->customerCanCancel()
                ? optional($this->resource->cancelExpiresAt())?->toIso8601String()
                : null,
            'can_cancel'                  => $this->resource->customerCanCancel(),
            'requires_delivery_otp'       => $this->requiresDeliveryOtp(),
            'delivery_otp'                => $this->visibleDeliveryOtp(),
            'delivery_otp_length'         => DeliveryOtpService::LENGTH,
            'updated_at'                  => AppLibrary::datetime($this->updated_at),
            'updated_at_iso'              => optional($this->updated_at)?->toIso8601String(),
            'restaurant_review_status'    => $this->restaurantReviewStatus(),
            'delivery_boy_review_status'  => $this->deliveryBoyReviewStatus(),
            'cash_back_amount'            => $this->posDetail?->received_amount - $this->total,
            'cash_back_currency_amount'   => AppLibrary::currencyAmountFormat($this->posDetail?->received_amount - $this->total),
            'user'                        => new UserResource($this->user),
            'order_address'               => new AddressResource($this->address),
            'restaurant'                  => new RestaurantResource($this->restaurant),
            'delivery_boy'                => new UserResource($this->deliveryBoy),
            'coupon'                      => new SimpleCouponResource($this->coupon?->coupon),
            'transaction'                 => new TransactionResource($this->transaction),
            'order_items'                 => OrderItemResource::collection($this->orderItems),
            'pos_detail'                  => new PosDetailsResource($this->posDetail),
            'return_images'               => $this->returnImages
        ];
    }

    private function paymentMethodLabel(): string
    {
        $method = (int) $this->payment_method;

        if ($method === PaymentGateway::CASH_ON_DELIVERY) {
            return $this->resource->isScanMenuOrder()
                ? trans('all.label.pay_at_counter')
                : trans('all.label.cash_on_delivery');
        }

        if ($this->transaction?->payment_method) {
            return (string) $this->transaction->payment_method;
        }

        return (string) (trans('payment_gateway.' . $method) ?: '');
    }

    private function posStatusLabel(): string
    {
        if ((int) $this->status === OrderStatus::CANCELED || (int) $this->status === OrderStatus::REJECTED) {
            return 'CANCELLED';
        }
        if ((int) $this->payment_status === \App\Enums\PaymentStatus::PAID
            || (int) $this->status === OrderStatus::DELIVERED) {
            return 'COMPLETED';
        }
        if ($this->billing_requested_at) {
            return 'BILLING';
        }
        if ((int) $this->status === OrderStatus::PREPARED) {
            return 'READY';
        }
        if ((int) $this->status === OrderStatus::PREPARING) {
            return 'IN_PROGRESS';
        }

        return 'OPEN';
    }

    private function requiresDeliveryOtp(): bool
    {
        return (int) $this->order_type === OrderType::DELIVERY
            && (int) $this->status === OrderStatus::OUT_FOR_DELIVERY;
    }

    private function visibleDeliveryOtp(): ?string
    {
        $showToCustomer = (int) $this->order_type === OrderType::DELIVERY
            && in_array((int) $this->status, [OrderStatus::PREPARED, OrderStatus::OUT_FOR_DELIVERY], true);

        if (!$showToCustomer || blank($this->delivery_otp)) {
            return null;
        }

        $user = Auth::user();
        if (!$user) {
            return null;
        }

        if ((int) ($user->myrole ?? 0) === EnumRole::DELIVERY_BOY) {
            return null;
        }

        return (string) $this->delivery_otp;
    }

    private function restaurantReviewStatus(): bool
    {
        if ($this->status == OrderStatus::DELIVERED) {
            $hours       = Settings::group('site')->get('site_rating_time') * 60 * 60;
            $updatedTime = (int) Carbon::now()->diffInSeconds($this->updated_at,true);
            if ((int)$hours >= $updatedTime) {
                return true;
            }
        }
        return false;
    }

    private function deliveryBoyReviewStatus(): bool
    {
        if ($this->delivery_boy_id > 0 && $this->status == OrderStatus::DELIVERED && $this->order_type == OrderType::DELIVERY) {
            $hours       = Settings::group('site')->get('site_rating_time') * 60 * 60;
            $updatedTime = (int) Carbon::now()->diffInSeconds($this->updated_at,true);
            if ((int)$hours >= $updatedTime) {
                return true;
            }
        }
        return false;
    }
}
