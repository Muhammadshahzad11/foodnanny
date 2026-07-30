<?php

namespace App\Services;


use App\Enums\Discount;
use Exception;
use App\Enums\Owner;
use App\Models\User;
use App\Models\Order;
use App\Models\Payout;
use App\Models\Refund;
use App\Models\Revenue;
use App\Enums\OrderType;
use App\Models\Statement;
use App\Enums\RevenueType;
use App\Models\Restaurant;
use App\Models\Transaction;
use App\Enums\PaymentStatus;
use App\Enums\StatementType;
use App\Enums\PaymentGateway;
use App\Libraries\AppLibrary;
use App\Enums\StatementDetail;
use App\Models\CompanyBalance;
use App\Enums\RevenueDetailEnum;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Libraries\QueryExceptionLibrary;
use Dipokhalder\Settings\Facades\Settings;


class StatementCalculationService
{

    /**
     * @throws Exception
     */
    public function restaurant(Order $order): void
    {
        try {
            DB::transaction(function () use ($order) {
                $order->load(['restaurant', 'coupon' => fn($query) => $query->with('coupon')]);

                $discount             = 0;
                $commission           = 0;
                $amount               = $order->subtotal;
                $commissionPercentage = Settings::group('site')->get('site_default_order_commission');

                if ($order->restaurant?->online_commission > 0) {
                    $commissionPercentage = $order->restaurant?->online_commission;
                }

                if ($order->coupon) {
                    if ($order->coupon->coupon?->owner === Owner::RESTAURANT_OWNER) {
                        $discount = $order->discount;
                        $amount   = $order->subtotal - $order->discount;
                    }
                }

                if ($commissionPercentage > 0) {
                    $commission = ($amount / 100) * $commissionPercentage;
                    $amount     -= $commission;
                }

                if ($order->total_tax > 0) {
                    $amount += $order->total_tax;
                }

                $info = [
                    'order_sl_number' => $order->order_serial_no,
                    'order_type'      => $order->order_type,
                    'date'            => date('d M Y, H:i'),
                    'type'            => StatementType::SALE,
                    'name'            => $order->restaurant->name,
                    'email'           => $order->restaurant->email,
                    'item_price'      => AppLibrary::flatAmountFormat($order->subtotal),
                    'tax'             => AppLibrary::flatAmountFormat($order->total_tax),
                    'commission'      => AppLibrary::flatAmountFormat($commission),
                    'discount'        => AppLibrary::flatAmountFormat($discount),
                    'total'           => AppLibrary::flatAmountFormat($amount)
                ];

                Statement::create([
                    'model_type' => Restaurant::class,
                    'model_id'   => $order->restaurant_id,
                    'date'       => date('Y-m-d H:i:s'),
                    'order_id'   => $order->id,
                    'type'       => StatementType::SALE,
                    'detail'     => StatementDetail::ITEMS_SALE,
                    'sign'       => '+',
                    'amount'     => AppLibrary::flatAmountFormat($amount),
                    'info'       => json_encode($info)
                ]);

                $order->restaurant->balance += $amount;
                $order->restaurant->save();
            });
        } catch (Exception $exception) {
            DB::rollBack();
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function restaurantTakeaway(Order $order): void
    {
        try {
            DB::transaction(function () use ($order) {
                $order->load(['restaurant', 'coupon' => fn($query) => $query->with('coupon')]);

                $commission           = 0;
                $amount               = $order->subtotal;
                $serviceFee           = 0;
                $commissionPercentage = Settings::group('site')->get('site_default_order_commission');

                if ($order->restaurant?->online_commission > 0) {
                    $commissionPercentage = $order->restaurant?->online_commission;
                }

                if ($order->coupon) {
                    if ($order->coupon->coupon?->owner === Owner::RESTAURANT_OWNER) {
                        $amount = $order->subtotal - $order->discount;
                    }
                }

                if ($commissionPercentage > 0) {
                    $commission = ($amount / 100) * $commissionPercentage;
                }

                if ($order->service_fee > 0) {
                    $serviceFee = $order->service_fee;
                }

                $total = AppLibrary::flatAmountFormat(AppLibrary::convertAmountFormat($commission) + AppLibrary::convertAmountFormat($serviceFee));

                $info = [
                    'order_sl_number' => $order->order_serial_no,
                    'order_type'      => $order->order_type,
                    'date'            => date('d M Y, H:i'),
                    'type'            => StatementType::COMMISSION,
                    'name'            => $order->restaurant->name,
                    'email'           => $order->restaurant->email,
                    'service_fee'     => AppLibrary::flatAmountFormat($serviceFee),
                    'commission'      => AppLibrary::flatAmountFormat($commission),
                    'total'           => AppLibrary::flatAmountFormat($total)
                ];

                Statement::create([
                    'model_type' => Restaurant::class,
                    'model_id'   => $order->restaurant_id,
                    'date'       => date('Y-m-d H:i:s'),
                    'order_id'   => $order->id,
                    'type'       => StatementType::COMMISSION,
                    'detail'     => StatementDetail::SERVICE_FEE,
                    'sign'       => '-',
                    'amount'     => -AppLibrary::flatAmountFormat($total),
                    'info'       => json_encode($info)
                ]);

                $order->restaurant->balance -= $total;
                $order->restaurant->save();
                $this->revenue($order, $total);
            });
        } catch (Exception $exception) {
            DB::rollBack();
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function restaurantPayout(Restaurant $restaurant, $amount): void
    {
        try {
            DB::transaction(function () use ($restaurant, $amount) {
                Statement::create([
                    'model_type' => Restaurant::class,
                    'model_id'   => $restaurant->id,
                    'date'       => date('Y-m-d H:i:s'),
                    'order_id'   => null,
                    'type'       => StatementType::PAYOUT,
                    'detail'     => StatementDetail::RELEASE_PAYOUT,
                    'sign'       => '-',
                    'amount'     => -$amount,
                    'info'       => json_encode([])
                ]);

                $restaurant->balance -= $amount;
                $restaurant->save();
            });
        } catch (Exception $exception) {
            DB::rollBack();
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function restaurantRefund(Order $order, $refundAmount, $deductionAmount): void
    {
        try {
            DB::transaction(function () use ($order, $refundAmount, $deductionAmount) {
                Statement::create([
                    'model_type' => Restaurant::class,
                    'model_id'   => $order->restaurant_id,
                    'date'       => date('Y-m-d H:i:s'),
                    'order_id'   => $order->id,
                    'type'       => StatementType::REFUND,
                    'detail'     => StatementDetail::REFUND,
                    'sign'       => '-',
                    'amount'     => -$deductionAmount,
                    'info'       => json_encode([])
                ]);

                $restaurant = Restaurant::find($order->restaurant_id);
                if (!blank($restaurant)) {
                    $restaurant->balance -= $deductionAmount;
                    $restaurant->save();

                    if ($order->payment_status == PaymentStatus::PAID) {
                        $paymentService = new PaymentService();
                        $paymentService->cashBackForRefund($order, 'credit', rand(111111111111111, 999999999999999), $refundAmount);
                    }
                }
            });
        } catch (Exception $exception) {
            DB::rollBack();
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }


    /**
     * @throws Exception
     */
    public function restaurantPOS(Order $order): void
    {
        try {
            DB::transaction(function () use ($order) {
                $order->load(['restaurant']);

                $commission           = 0;
                $amount               = $order->subtotal;
                $commissionPercentage = Settings::group('site')->get('site_default_pos_commission');

                if ($order->restaurant?->pos_commission > 0) {
                    $commissionPercentage = $order->restaurant?->pos_commission;
                }

                if ($order->discount > 0) {
                    $amount = $order->subtotal - $order->discount;
                }

                if ($commissionPercentage > 0) {
                    $commission = ($amount / 100) * $commissionPercentage;
                }

                $total = AppLibrary::flatAmountFormat(AppLibrary::convertAmountFormat($commission));

                $info = [
                    'order_sl_number' => $order->order_serial_no,
                    'order_type'      => $order->order_type,
                    'date'            => date('d M Y, H:i'),
                    'type'            => StatementType::COMMISSION,
                    'name'            => $order->restaurant->name,
                    'email'           => $order->restaurant->email,
                    'commission'      => AppLibrary::flatAmountFormat($commission),
                    'total'           => AppLibrary::flatAmountFormat($total)
                ];

                Statement::create([
                    'model_type' => Restaurant::class,
                    'model_id'   => $order->restaurant_id,
                    'date'       => date('Y-m-d H:i:s'),
                    'order_id'   => $order->id,
                    'type'       => StatementType::COMMISSION,
                    'detail'     => StatementDetail::SERVICE_FEE,
                    'sign'       => '-',
                    'amount'     => -AppLibrary::flatAmountFormat($total),
                    'info'       => json_encode($info)
                ]);

                $order->restaurant->balance -= $total;
                $order->restaurant->save();
                $this->revenue($order, $total);
            });
        } catch (Exception $exception) {
            DB::rollBack();
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function restaurantDining(Order $order): void
    {
        try {
            DB::transaction(function () use ($order) {
                $order->load(['restaurant']);

                $commission           = 0;
                $amount               = $order->subtotal;
                $commissionPercentage = Settings::group('site')->get('site_default_dining_commission');

                if ($order->restaurant?->dining_commission > 0) {
                    $commissionPercentage = $order->restaurant?->dining_commission;
                }

                if ($order->discount > 0) {
                    $amount = $order->subtotal - $order->discount;
                }

                if ($commissionPercentage > 0) {
                    $commission = ($amount / 100) * $commissionPercentage;
                }

                $total = AppLibrary::flatAmountFormat(AppLibrary::convertAmountFormat($commission));

                $info = [
                    'order_sl_number' => $order->order_serial_no,
                    'order_type'      => $order->order_type,
                    'date'            => date('d M Y, H:i'),
                    'type'            => StatementType::COMMISSION,
                    'name'            => $order->restaurant->name,
                    'email'           => $order->restaurant->email,
                    'commission'      => AppLibrary::flatAmountFormat($commission),
                    'total'           => AppLibrary::flatAmountFormat($total)
                ];

                Statement::create([
                    'model_type' => Restaurant::class,
                    'model_id'   => $order->restaurant_id,
                    'date'       => date('Y-m-d H:i:s'),
                    'order_id'   => $order->id,
                    'type'       => StatementType::COMMISSION,
                    'detail'     => StatementDetail::SERVICE_FEE,
                    'sign'       => '-',
                    'amount'     => -AppLibrary::flatAmountFormat($total),
                    'info'       => json_encode($info)
                ]);

                $order->restaurant->balance -= $total;
                $order->restaurant->save();
                $this->revenue($order, $total);
            });
        } catch (Exception $exception) {
            DB::rollBack();
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }


    /**
     * @throws Exception
     */
    public function restaurantDiningCashback(Order $order): void
    {
        try {
            DB::transaction(function () use ($order) {
                $order->load(['restaurant']);

                $info = [
                    'order_sl_number' => $order->order_serial_no,
                    'order_type'      => $order->order_type,
                    'date'            => date('d M Y, H:i'),
                    'type'            => StatementType::CASHBACK,
                    'name'            => $order->restaurant->name,
                    'email'           => $order->restaurant->email,
                    'cashback'        => AppLibrary::flatAmountFormat($order->total),
                    'total'           => AppLibrary::flatAmountFormat($order->total)
                ];

                Statement::create([
                    'model_type' => Restaurant::class,
                    'model_id'   => $order->restaurant_id,
                    'date'       => date('Y-m-d H:i:s'),
                    'order_id'   => $order->id,
                    'type'       => StatementType::CASHBACK,
                    'detail'     => StatementDetail::CASHBACK,
                    'sign'       => '+',
                    'amount'     => AppLibrary::flatAmountFormat($order->total),
                    'info'       => json_encode($info)
                ]);

                $order->restaurant->balance += $order->total;
                $order->restaurant->save();
            });
        } catch (Exception $exception) {
            DB::rollBack();
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }


    /**
     * @throws Exception
     */
    public function deliveryBoy(Order $order): void
    {
        try {
            DB::transaction(function () use ($order) {
                $order->load('deliveryBoy');

                $commission           = 0;
                $amount               = $order->delivery_fee;
                $deliveryFee          = $order->delivery_fee;
                $commissionPercentage = Settings::group('site')->get('site_default_delivery_commission');

                if ($order->delivery_fee <= 0) {
                    $amount      = $order->extra_delivery_fee;
                    $deliveryFee = $order->extra_delivery_fee;
                }

                if ($commissionPercentage > 0) {
                    $commission = ($amount / 100) * $commissionPercentage;
                    $amount     -= $commission;
                }

                if ($order->rider_tip > 0) {
                    $amount += $order->rider_tip;
                }

                $info = [
                    'order_sl_number' => $order->order_serial_no,
                    'order_type'      => $order->order_type,
                    'date'            => date('d M Y, H:i'),
                    'type'            => StatementType::DELIVERY,
                    'name'            => $order->deliveryBoy->name,
                    'email'           => $order->deliveryBoy->email,
                    'delivery_fee'    => AppLibrary::flatAmountFormat($deliveryFee),
                    'rider_tip'       => AppLibrary::flatAmountFormat($order->rider_tip),
                    'commission'      => AppLibrary::flatAmountFormat($commission),
                    'total'           => AppLibrary::flatAmountFormat($amount)
                ];

                Statement::create([
                    'model_type' => User::class,
                    'model_id'   => $order->delivery_boy_id,
                    'date'       => date('Y-m-d H:i:s'),
                    'order_id'   => $order->id,
                    'type'       => StatementType::DELIVERY,
                    'detail'     => StatementDetail::DELIVERY_FEE_AND_TIP,
                    'sign'       => '+',
                    'amount'     => AppLibrary::flatAmountFormat($amount),
                    'info'       => json_encode($info)
                ]);

                if ($order->payment_method === PaymentGateway::CASH_ON_DELIVERY) {
                    $order->deliveryBoy->collection += $order->total;
                }

                $order->deliveryBoy->balance += $amount;
                $order->deliveryBoy->save();
            });
        } catch (Exception $exception) {
            DB::rollBack();
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function deliveryBoyPayout(User $deliveryBoy, $amount): void
    {
        try {
            DB::transaction(function () use ($deliveryBoy, $amount) {
                Statement::create([
                    'model_type' => User::class,
                    'model_id'   => $deliveryBoy->id,
                    'date'       => date('Y-m-d H:i:s'),
                    'order_id'   => null,
                    'type'       => StatementType::PAYOUT,
                    'detail'     => StatementDetail::RELEASE_PAYOUT,
                    'sign'       => '-',
                    'amount'     => -$amount,
                    'info'       => json_encode([])
                ]);

                $deliveryBoy->balance -= $amount;
                $deliveryBoy->save();
            });
        } catch (Exception $exception) {
            DB::rollBack();
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function deliveryBoyRefund(Order $order, $refundAmount, $deductionAmount): void
    {
        try {
            DB::transaction(function () use ($order, $refundAmount, $deductionAmount) {
                Statement::create([
                    'model_type' => User::class,
                    'model_id'   => $order->delivery_boy_id,
                    'date'       => date('Y-m-d H:i:s'),
                    'order_id'   => $order->id,
                    'type'       => StatementType::REFUND,
                    'detail'     => StatementDetail::REFUND,
                    'sign'       => '-',
                    'amount'     => -$deductionAmount,
                    'info'       => json_encode([])
                ]);

                $deliveryBoy = User::find($order->delivery_boy_id);
                if (!blank($deliveryBoy)) {
                    $deliveryBoy->balance -= $deductionAmount;
                    $deliveryBoy->save();

                    if ($order->payment_status == PaymentStatus::PAID) {
                        $paymentService = new PaymentService();
                        $paymentService->cashBackForRefund($order, 'credit', rand(111111111111111, 999999999999999), $refundAmount);
                    }
                }
            });
        } catch (Exception $exception) {
            DB::rollBack();
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function reversePayout(Payout $payout): void
    {
        try {
            DB::transaction(function () use ($payout) {
                Statement::create([
                    'model_type' => $payout->model_type,
                    'model_id'   => $payout->model_id,
                    'date'       => date('Y-m-d H:i:s'),
                    'order_id'   => null,
                    'type'       => StatementType::REVERSE,
                    'detail'     => StatementDetail::REVERSE_PAYOUT,
                    'sign'       => '+',
                    'amount'     => $payout->amount,
                    'info'       => json_encode([])
                ]);

                if ($payout->model_type === Restaurant::class) {
                    $restaurant = Restaurant::find($payout->model_id);
                    if ($restaurant) {
                        $restaurant->balance += $payout->amount;
                        $restaurant->save();
                    }
                } elseif ($payout->model_type === User::class) {
                    $deliveryBoy = User::find($payout->model_id);
                    if ($deliveryBoy) {
                        $deliveryBoy->balance += $payout->amount;
                        $deliveryBoy->save();
                    }
                }
            });
        } catch (Exception $exception) {
            DB::rollBack();
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function reverseRefund(Refund $refund): void
    {
        try {
            DB::transaction(function () use ($refund) {
                $order = Order::where(['order_serial_no' => $refund->order_serial_no])->first();
                if (!blank($order)) {
                    $statement = Statement::where(['order_id' => $order->id, 'type' => StatementType::REFUND])->first();
                    if (!blank($statement)) {
                        $model = null;
                        if ($statement->model_type == Restaurant::class) {
                            $model = Restaurant::find($statement->model_id);
                        } elseif ($statement->model_type == User::class) {
                            $model = User::find($statement->model_id);
                        }

                        if (!blank($model)) {
                            $model->balance += abs($statement->amount);
                            $model->save();

                            $transaction = Transaction::where(['order_id' => $order->id, 'payment_method' => 'credit', 'type' => 'cash_back'])->first();
                            if (!blank($transaction)) {
                                $user = User::find($order->user_id);
                                if (!blank($user)) {
                                    $user->balance -= abs($refund->refund_amount);
                                    $user->save();
                                }
                                $transaction->delete();
                            }
                        }
                        $statement->delete();
                    }
                }
            });
        } catch (Exception $exception) {
            DB::rollBack();
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function ownerRevenueFromDeliveryBoy(Order $order): void
    {
        try {
            $statementSum = Statement::where('order_id', $order->id)->sum('amount');
            $totalRevenue = $order->total - $statementSum;
            $this->revenue($order, $totalRevenue);
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }

    }

    /**
     * @throws Exception
     */
    public function ownerRevenueFromRestaurant(Order $order): void
    {
        try {
            $statementSum = Statement::where('order_id', $order->id)->sum('amount');
            $totalRevenue = $order->total - $statementSum;
            $this->revenue($order, $totalRevenue);
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    private function revenue(order $order, $total): void
    {
        try {
            DB::transaction(function () use ($order, $total) {
                $discount             = 0;
                $restaurantCommission = 0;
                $deliveryCommission   = 0;
                $restaurantStatement  = null;

                if ($order->order_type === OrderType::DELIVERY) {
                    $restaurantStatement = Statement::where(['order_id' => $order->id, 'type' => StatementType::SALE])->first();
                } elseif ($order->order_type === OrderType::TAKEAWAY) {
                    $restaurantStatement = Statement::where(['order_id' => $order->id, 'type' => StatementType::COMMISSION])->first();
                    if(!$restaurantStatement) {
                        $restaurantStatement = Statement::where(['order_id' => $order->id, 'type' => StatementType::SALE])->first();
                    }
                } elseif ($order->order_type === OrderType::POS) {
                    $restaurantStatement = Statement::where(['order_id' => $order->id, 'type' => StatementType::COMMISSION])->first();
                } elseif ($order->order_type === OrderType::DINING_TABLE) {
                    $restaurantStatement = Statement::where(['order_id' => $order->id, 'type' => StatementType::COMMISSION])->first();
                }

                if (!blank($restaurantStatement)) {
                    $restaurantStatement = json_decode($restaurantStatement->info, true);
                    if (count($restaurantStatement) > 0 && isset($restaurantStatement['commission']) && $restaurantStatement['commission'] > 0) {
                        $restaurantCommission = $restaurantStatement['commission'];
                    }
                }

                $deliveryBoyStatement = Statement::where(['order_id' => $order->id, 'type' => StatementType::DELIVERY])->first();
                if (!blank($deliveryBoyStatement)) {
                    $deliveryBoyStatement = json_decode($deliveryBoyStatement->info, true);
                    if (count($deliveryBoyStatement) > 0 && isset($deliveryBoyStatement['commission']) && $deliveryBoyStatement['commission'] > 0) {
                        $deliveryCommission = $deliveryBoyStatement['commission'];
                    }
                }

                if ($order?->coupon?->coupon?->owner == Owner::ADMIN && $order?->coupon?->coupon?->type == Discount::DEFAULT) {
                    $discount = $order->discount;
                } elseif ($order?->coupon?->coupon?->owner == Owner::ADMIN && $order?->coupon?->coupon?->type == Discount::FREE_DELIVERY) {
                    $discount = $order->extra_delivery_fee;
                }


                Revenue::create([
                    'order_id'       => $order->id,
                    'type'           => RevenueType::SALE,
                    'detail'         => RevenueDetailEnum::SERVICE_AND_COMMISSION_FEE,
                    'sign'           => $total >= 0 ? '+' : '-',
                    'order_amount'   => $order->total,
                    'revenue_amount' => AppLibrary::convertAmountFormat($total),
                    'date'           => date('Y-m-d H:i:s'),
                    'info'           => json_encode([
                        'order_sl_number'       => $order->order_serial_no,
                        'order_type'            => $order->order_type,
                        'date'                  => date('d M Y, H:i'),
                        'discount'              => AppLibrary::flatAmountFormat($discount),
                        'service_fee'           => AppLibrary::flatAmountFormat($order->service_fee),
                        'restaurant_commission' => AppLibrary::flatAmountFormat($restaurantCommission),
                        'delivery_commission'   => AppLibrary::flatAmountFormat($deliveryCommission),
                        'total'                 => AppLibrary::flatAmountFormat($total)
                    ])
                ]);

                $companyBalance          = CompanyBalance::find(1);
                $companyBalance->balance += AppLibrary::convertAmountFormat($total);
                $companyBalance->save();
            });
        } catch (Exception $exception) {
            DB::rollBack();
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }
}
