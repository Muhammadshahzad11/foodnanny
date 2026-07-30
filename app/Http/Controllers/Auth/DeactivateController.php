<?php

namespace App\Http\Controllers\Auth;

use Exception;
use App\Enums\Role;
use App\Models\Order;
use App\Enums\OrderStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;


class DeactivateController extends Controller
{
    function deleteAccount(Request $request): \Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            DB::transaction(function () use ($request) {
                $userRole   = $request->user()->myRole;
                $checkOrder = Order::where('user_id', auth()->user()->id)->whereNotIn('status', [OrderStatus::DELIVERED, OrderStatus::CANCELED, OrderStatus::REJECTED, OrderStatus::RETURNED])->first();

                if ($userRole !== Role::CUSTOMER) {
                    throw new Exception(trans('all.message.only_customer_delete'), 422);
                } else if ($checkOrder) {
                    throw new Exception(trans('all.message.account_not_delete'), 422);
                } else {
                    $request->user()->delete();
                    session()->flush();
                    $request->user()->currentAccessToken()->delete();
                }
            });
            return response(['status' => true, 'message' => trans("all.message.account_delete_success")]);
        } catch (Exception $exception) {
            DB::rollBack();
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
