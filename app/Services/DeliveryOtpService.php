<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Enums\OrderType;
use App\Models\FrontendOrder;
use App\Models\Order;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DeliveryOtpService
{
    public const LENGTH = 4;

    public function isRequired(Model $order): bool
    {
        return (int) $order->order_type === OrderType::DELIVERY
            && (int) $order->status === OrderStatus::OUT_FOR_DELIVERY;
    }

    public function ensure(Model $order, bool $force = false): string
    {
        if ((int) $order->order_type !== OrderType::DELIVERY) {
            return '';
        }

        [$otp, $verifiedAt] = DB::transaction(function () use ($order, $force) {
            /** @var Model|null $locked */
            $locked = $order->newQuery()->whereKey($order->getKey())->lockForUpdate()->first();
            if (!$locked) {
                throw new Exception(trans('all.message.something_wrong'), 404);
            }

            if (!$force && !blank($locked->delivery_otp)) {
                return [(string) $locked->delivery_otp, $locked->delivery_otp_verified_at];
            }

            $generated = str_pad(
                (string) random_int(0, (10 ** self::LENGTH) - 1),
                self::LENGTH,
                '0',
                STR_PAD_LEFT
            );
            $locked->delivery_otp = $generated;
            $locked->delivery_otp_verified_at = null;
            $locked->save();

            return [$generated, null];
        }, 3);

        // Keep the caller's in-memory model consistent with the locked row.
        $order->delivery_otp = $otp;
        $order->delivery_otp_verified_at = $verifiedAt;

        return $otp;
    }

    /**
     * @throws Exception
     */
    public function assertValid(Order $order, ?string $input): void
    {
        if ((int) $order->order_type !== OrderType::DELIVERY) {
            return;
        }

        $this->ensure($order);

        $given = preg_replace('/\D+/', '', (string) $input);
        if (strlen($given) !== self::LENGTH) {
            throw new Exception(trans('all.message.delivery_otp_required'), 422);
        }

        if (!hash_equals((string) $order->delivery_otp, $given)) {
            throw new Exception(trans('all.message.invalid_delivery_otp'), 422);
        }
    }

    public function markVerified(Order $order): void
    {
        $order->delivery_otp_verified_at = now();
    }
}
