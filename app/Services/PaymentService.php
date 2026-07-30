<?php

namespace App\Services;

use Exception;
use App\Enums\Ask;
use App\Models\User;
use App\Enums\Status;
use App\Models\OrderItem;
use App\Models\Transaction;
use App\Enums\PaymentStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Libraries\QueryExceptionLibrary;

class PaymentService
{

    public object $transaction;

    /**
     * @throws Exception
     */
    public function payment($order, $gatewaySlug, $transactionNo): object
    {
        try {
            DB::transaction(function () use ($order, $gatewaySlug, $transactionNo) {
                $transaction = Transaction::where(['order_id' => $order->id])->first();
                if (!$transaction) {
                    $transaction = Transaction::create([
                        'restaurant_id'  => $order->restaurant_id,
                        'order_id'       => $order->id,
                        'transaction_no' => $transactionNo,
                        'amount'         => $order->total,
                        'payment_method' => $gatewaySlug,
                        'sign'           => '+',
                        'type'           => 'payment'
                    ]);
                }
                $this->transaction     = $transaction;
                $order->active         = Ask::YES;
                $order->payment_status = PaymentStatus::PAID;
                $order->save();
                OrderItem::where(['order_id' => $order->id, 'status' => Status::INACTIVE])?->update(['status' => Status::ACTIVE]);
            });
            return $this->transaction;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            DB::rollBack();
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function cashBack($order, $gatewaySlug, $transactionNo): object
    {
        try {
            DB::transaction(function () use ($order, $gatewaySlug, $transactionNo) {
                $this->transaction = Transaction::where(['order_id' => $order->id])->first();
                if ($this->transaction) {
                    $this->transaction = Transaction::create([
                        'restaurant_id'  => $order->restaurant_id,
                        'order_id'       => $order->id,
                        'transaction_no' => $transactionNo,
                        'amount'         => $order->total,
                        'payment_method' => $gatewaySlug,
                        'sign'           => '-',
                        'type'           => 'cash_back'
                    ]);

                    $user = User::find($order->user_id);
                    if ($user) {
                        $user->balance = ($user->balance + $order->total);
                        $user->save();
                    }
                }
            });
            return $this->transaction;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            DB::rollBack();
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function cashBackForRefund($order, $gatewaySlug, $transactionNo, $amount): object
    {
        try {
            DB::transaction(function () use ($order, $gatewaySlug, $transactionNo, $amount) {
                $this->transaction = Transaction::create([
                    'restaurant_id'  => $order->restaurant_id,
                    'order_id'       => $order->id,
                    'transaction_no' => $transactionNo,
                    'amount'         => $amount,
                    'payment_method' => $gatewaySlug,
                    'sign'           => '-',
                    'type'           => 'cash_back'
                ]);

                $user = User::find($order->user_id);
                if ($user) {
                    $user->balance = ($user->balance + $amount);
                    $user->save();
                }
            });
            return $this->transaction;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            DB::rollBack();
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }
}
